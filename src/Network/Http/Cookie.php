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
namespace LaravelBoot\Foundation\Network\Http;

use Illuminate\Support\Facades\Config;
use LaravelBoot\Foundation\Exception\InvalidArgumentException;
use LaravelBoot\Foundation\Network\Http\Request\Request;
use swoole_http_response as SwooleHttpResponse;

class Cookie
{
    private $configKey = 'cookie';
    private $config;
    private $request;
    private $response;

    private static $domainWhiteList;

    public function __construct(Request $request, SwooleHttpResponse $swooleResponse)
    {
        $this->init($request, $swooleResponse);
    }

    private function init(Request $request, SwooleHttpResponse $swooleResponse)
    {
        $config = Config::get($this->configKey, null);
        if (!$config) {
            throw new InvalidArgumentException('Cookie config is required');
        }
        $this->config = $config;
        $this->request = $request;
        $this->response = $swooleResponse;
    }

    public function get($key, $default = null)
    {
        $cookies = $this->request->cookies;
        if (!$key) {
            yield $default;
        }

        yield $cookies->get($key, $default);
    }

    public function set($key, $value = null, $expire = null, $path = null, $domain = null, $secure = null, $httpOnly = null)
    {
        if (!$key) {
            return false;
        }
        if (null === $expire) {
            $expire = isset($this->config['expire']) ? $this->config['expire'] : 0;
        }
        $expire = time() + (int)$expire;

        $path = (null !== $path) ? $path : $this->config['path'];
        $domain = (null !== $domain) ? $domain : $this->getDomain($this->request->getHost() ?: "");
        $secure = (null !== $secure) ? $secure : $this->config['secure'];
        $httpOnly = (null !== $httpOnly) ? $httpOnly : $this->config['httponly'];

        $this->response->cookie($key, $value, $expire, $path, $domain, $secure, $httpOnly);
    }

    private function getDomain($host)
    {
        if (static::$domainWhiteList === null) {
            static::initDomainWhiteList();
        }

        $hostLen = strlen($host);
        foreach (static::$domainWhiteList as $domain => $len) {
            if ($hostLen < $len) {
                continue;
            }

            if (substr(rtrim($host), -$len) === $domain) {
                return $domain;
            }
        }
        return $host;
    }

    private function initDomainWhiteList()
    {
        $domainList = [
            '.17huo.com',
            '.xunyix.com'
        ];

        array_push($domainList, ...(array)Config::get("cookie.domain", []));

        foreach ($domainList as $domain) {
            static::$domainWhiteList[$domain] = strlen($domain);
        }

        arsort(static::$domainWhiteList);
    }
}
