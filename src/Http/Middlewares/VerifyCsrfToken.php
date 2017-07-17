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
namespace LaravelBoot\Foundation\Http\Middlewares;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Events\Dispatcher;
use Illuminate\Session\TokenMismatchException;
use LaravelBoot\Foundation\Application;
use LaravelBoot\Foundation\Http\Events\CsrfTokenRegister;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Class VerifyCsrfToken.
 */
class VerifyCsrfToken
{
    /**
     * @var \LaravelBoot\Foundation\Application
     */
    protected $app;

    /**
     * @var \Illuminate\Events\Dispatcher
     */
    protected $events;

    /**
     * @var \Illuminate\Contracts\Encryption\Encrypter
     */
    protected $encrypter;

    /**
     * @var array
     */
    protected $except = [
    ];

    /**
     * @param \LaravelBoot\Foundation\Application             $app
     * @param \Illuminate\Events\Dispatcher              $events
     * @param \Illuminate\Contracts\Encryption\Encrypter $encrypter
     */
    public function __construct(Application $app, Dispatcher $events, Encrypter $encrypter)
    {
        $this->app = $app;
        $this->events = $events;
        $this->encrypter = $encrypter;
    }

    /**
     * Middleware handler.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Illuminate\Session\TokenMismatchException
     */
    public function handle($request, Closure $next)
    {
        $this->events->dispatch(new CsrfTokenRegister($this));
        if ($this->isReading($request) || $this->runningUnitTests() || $this->shouldPassThrough($request) || $this->tokensMatch($request)) {
            return $this->addCookieToResponse($request, $next($request));
        }
        throw new TokenMismatchException();
    }

    /**
     * Determine if the request has a URI that should pass through CSRF verification.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    protected function shouldPassThrough($request)
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }
            if ($request->is($except)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the application is running unit tests.
     *
     * @return bool
     */
    protected function runningUnitTests()
    {
        return $this->app->runningInConsole() && $this->app->runningUnitTests();
    }

    /**
     * Determine if the session and input CSRF tokens match.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    protected function tokensMatch($request)
    {
        $sessionToken = $request->session()->token();
        $token = $request->input('_token') ?: $request->header('X-CSRF-TOKEN');
        if (!$token && $header = $request->header('X-XSRF-TOKEN')) {
            $token = $this->encrypter->decrypt($header);
        }
        if (!is_string($sessionToken) || !is_string($token)) {
            return false;
        }

        return hash_equals($sessionToken, $token);
    }

    /**
     * Add the CSRF token to the response cookies.
     *
     * @param \Illuminate\Http\Request                   $request
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function addCookieToResponse($request, $response)
    {
        $config = config('session');
        $response->headers->setCookie(new Cookie('XSRF-TOKEN', $request->session()->token(),
            Carbon::now()->getTimestamp() + 60 * $config['lifetime'], $config['path'], $config['domain'],
            $config['secure'], false));

        return $response;
    }

    /**
     * Determine if the HTTP request uses a ‘read’ verb.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    protected function isReading($request)
    {
        return in_array($request->method(), [
            'HEAD',
            'GET',
            'OPTIONS',
        ]);
    }

    /**
     * Register except to this.
     *
     * @param $excepts
     */
    public function registerExcept($excepts)
    {
        $this->except = array_merge($this->except, (array)$excepts);
    }
}
