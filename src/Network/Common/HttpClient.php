<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/17 10:48
 * @version
 */
namespace LaravelBoot\Foundation\Network\Common;

use LaravelBoot\Foundation\Contracts\Async;
use Illuminate\Support\Facades\Config;
use LaravelBoot\Foundation\Coroutine\Task;
use LaravelBoot\Foundation\Exception\InvalidArgumentException;
use LaravelBoot\Foundation\Network\Common\Exception\DnsLookupTimeoutException;
use LaravelBoot\Foundation\Network\Common\Exception\HostNotFoundException;
use LaravelBoot\Foundation\Utility\Timer;
use LaravelBoot\Foundation\Network\Common\Exception\HttpClientTimeoutException;
use LaravelBoot\Foundation\Network\Tcp\RpcContext;
//use Zan\Framework\Sdk\Trace\Constant;
//use Zan\Framework\Sdk\Trace\DebuggerTrace;
//use Zan\Framework\Sdk\Trace\Trace;
use LaravelBoot\Foundation\Contracts\Context;

class HttpClient implements Async
{
    const GET = 'GET';
    const POST = 'POST';

    /** @var  \swoole_http_client */
    private $client;

    private $host;
    private $port;
    private $ssl;

    /**
     * @var int [millisecond]
     */
    private $timeout;

    private $uri;
    private $method;

    private $params;
    private $header = [];
    private $body;

    private $callback;

    /** @var RpcContext */
    private $rpcContext;

    /** @var Trace */
    private $trace;
    private $traceHandle;

    /** @var DebuggerTrace  */
    private $debuggerTrace;

    private $useHttpProxy = false;

    public function __construct($host, $port = 80, $ssl = false)
    {
        $this->host = $host;
        $this->port = $port;
        $this->ssl = $ssl;
    }

    public static function newInstance($host, $port = 80, $ssl = false)
    {
        return new static($host, $port, $ssl);
    }

    public static function newInstanceUsingProxy($host, $port = 80, $ssl = false)
    {
        $instance = new static($host, $port, $ssl);
        $instance->useHttpProxy = true;

        return $instance;
    }


    public function get($uri = '', $params = [], $timeout = 3000)
    {
        $this->setMethod(self::GET);
        $this->setTimeout($timeout);
        $this->setUri($uri);
        $this->setParams($params);

        yield $this->build();
    }

    public function post($uri = '', $params = [], $timeout = 3000)
    {
        $this->setMethod(self::POST);
        $this->setTimeout($timeout);
        $this->setUri($uri);
        $this->setParams($params);

        yield $this->build();
    }

    public function postJson($uri = '', $params = [], $timeout = 3000)
    {
        $this->setMethod(self::POST);
        $this->setTimeout($timeout);
        $this->setUri($uri);
        $this->setParams(json_encode($params));

        $this->setHeader([
            'Content-Type' => 'application/json'
        ]);

        yield $this->build();
    }

    public function execute($callback, $task)
    {
        /** @var Task $task */
        $ctx = $task->getContext();
        $this->setCallback($this->getCallback($callback))->handle($ctx);
    }

    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    public function setUri($uri)
    {
        if (empty($uri)) {
            $uri .= '/';
        }
        $this->uri = $uri;
        return $this;
    }

    public function setTimeout($timeout)
    {
        if (null !== $timeout) {
            if ($timeout < 0 || $timeout > 60000) {
                throw new InvalidArgumentException("Timeout must be between 0-60 seconds, $timeout is given");
            }
        }
        $this->timeout = $timeout;
        return $this;
    }

    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }

    public function setHeader(array $header)
    {
        $this->header = array_merge($this->header, $header);
        return $this;
    }

    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    public function build()
    {
        if ($this->method === 'GET') {
            if (!empty($this->params)) {
                $this->uri = $this->uri . '?' . http_build_query($this->params);
            }
        } else if ($this->method === 'POST') {
            $body = $this->params;

            $this->setBody($body);
        }

        yield $this;
    }

    public function setCallback(Callable $callback)
    {
        $this->callback = $callback;
        return $this;
    }

    public function handle(Context $ctx = null)
    {
        if ($ctx) {
            //$this->trace = $ctx->get("trace");
            //$this->debuggerTrace = $ctx->get('debugger_trace');
            $this->rpcContext = $ctx->get("rpc-context");
        }

        if ($this->useHttpProxy) {
            $host = Config::get("http_proxy.host");
            $port = Config::get("http_proxy.port", 80);
            if (empty($host)) {
                throw new \InvalidArgumentException("Missing http proxy config");
            }
        } else {
            $host = $this->host;
            $port = $this->port;
        }

        $dnsCallbackFn = function($host, $ip) use ($port) {
            if ($ip) {
                $this->request($ip, $port);
            } else {
                $this->whenHostNotFound($host);
            }
        };

        if ($this->timeout === null) {
            DnsClient::lookupWithoutTimeout($host, $dnsCallbackFn);
        } else {
            DnsClient::lookup($host, $dnsCallbackFn, [$this, "dnsLookupTimeout"], $this->timeout);
        }
    }

    public function request($ip, $port)
    {
        if ($this->useHttpProxy) {
            $this->client = new \swoole_http_client($ip, $port);
        } else {
            $this->client = new \swoole_http_client($ip, $port, $this->ssl);
        }

        $this->buildHeader();

        if ($this->trace) {
            $this->traceHandle = $this->trace->transactionBegin(Constant::HTTP_CALL, $this->host . $this->uri);
        }
        if ($this->debuggerTrace instanceof DebuggerTrace) {
            $scheme = $this->ssl ? "https://" : "http://";
            $name = "{$this->method}-{$scheme}{$this->host}:{$this->port}{$this->uri}";
            $this->debuggerTrace->beginTransaction(Constant::HTTP, $name, [
                'params' => $this->params,
                'body' => $this->body,
                'header' => $this->header,
                'use_http_proxy' => $this->useHttpProxy,
            ]);
        }

        if (null !== $this->timeout) {
            Timer::after($this->timeout, [$this, 'checkTimeout'], spl_object_hash($this));
        }

        if('GET' === $this->method){
            if ($this->trace) {
                $this->trace->logEvent(Constant::GET, Constant::SUCCESS);
            }
            $this->client->get($this->uri, [$this,'onReceive']);
        } elseif ('POST' === $this->method){
            if ($this->trace) {
                $this->trace->logEvent(Constant::POST, Constant::SUCCESS, $this->body);
            }
            $this->client->post($this->uri,$this->body, [$this, 'onReceive']);
        } else {
            if ($this->trace) {
                $this->trace->logEvent($this->method, Constant::SUCCESS, $this->body);
            }

            $this->client->setMethod($this->method);
            if ($this->body) {
                $this->client->setData($this->body);
            }
            $this->client->execute($this->uri, [$this,'onReceive']);
        }
    }

    private function buildHeader()
    {
        if ($this->port !== 80) {
            $this->header['Host'] = $this->host . ':' . $this->port;
        } else {
            $this->header['Host'] = $this->host;
        }

        if ($this->ssl) {
            $this->header['Scheme'] = 'https';
        }

        if ($this->debuggerTrace instanceof DebuggerTrace) {
            $this->header[DebuggerTrace::KEY] = $this->debuggerTrace->getKey();
        }

        if ($this->rpcContext instanceof RpcContext) {
            $pairs = $this->rpcContext->get();
            foreach ($pairs as $key => $value) {
                if (is_scalar($value)) {
                    $this->header[$key] = strval($value);
                }
            }
        }

        $this->client->setHeaders($this->header);
    }

    public function onReceive($cli)
    {
        Timer::clearAfterJob(spl_object_hash($this));
        if ($this->trace) {
            $this->trace->commit($this->traceHandle, Constant::SUCCESS);
        }
        if ($this->debuggerTrace instanceof DebuggerTrace) {
            $res = [
                "code" => $cli->statusCode,
                "header" => $cli->headers,
                "body" => mb_convert_encoding($cli->body, 'UTF-8', 'UTF-8'),
            ];

            $this->debuggerTrace->commit("info", $res);
        }

        $response = new Response($cli->statusCode, $cli->headers, $cli->body);
        call_user_func($this->callback, $response);
        $this->client->close();
    }

    public function whenHostNotFound($host)
    {
        $ex = new HostNotFoundException("", 408, null, [ "host" => $host ]);
        call_user_func($this->callback, null, $ex);
    }

    private function getCallback(callable $callback)
    {
        return function($response, $exception = null) use ($callback) {
            call_user_func($callback, $response, $exception);
        };
    }

    public function checkTimeout()
    {
        $this->client->close();

        $message = sprintf(
            '[http request timeout] host:%s port:%s uri:%s method:%s ',
            $this->host,
            $this->port,
            $this->uri,
            $this->method
        );
        $metaData = [
            'host' => $this->host,
            'port' => $this->port,
            'ssl' => $this->ssl,
            'uri' => $this->uri,
            'method' => $this->method,
            'params' => $this->params,
            'body' => $this->body,
            'header' => $this->header,
            'timeout' => $this->timeout,
            'use_http_proxy' => $this->useHttpProxy,
        ];

        $exception = new HttpClientTimeoutException($message, 408, null, $metaData);

        if ($this->trace) {
            $this->trace->commit($this->traceHandle, $exception);
        }
        if ($this->debuggerTrace instanceof DebuggerTrace) {
            $this->debuggerTrace->commit("warn", $exception);
        }
        call_user_func($this->callback, null, $exception);
    }

    public function dnsLookupTimeout()
    {
        $message = sprintf(
            '[http dns lookup timeout] host:%s port:%s uri:%s method:%s ',
            $this->host,
            $this->port,
            $this->uri,
            $this->method
        );
        $metaData = [
            'host' => $this->host,
            'port' => $this->port,
            'ssl' => $this->ssl,
            'uri' => $this->uri,
            'method' => $this->method,
            'params' => $this->params,
            'body' => $this->body,
            'header' => $this->header,
            'timeout' => $this->timeout,
            'use_http_proxy' => $this->useHttpProxy,
        ];

        $exception = new DnsLookupTimeoutException($message, 408, null, $metaData);

        call_user_func($this->callback, null, $exception);
    }
}