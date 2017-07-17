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

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as IlluminateAuthenticate;
use Illuminate\Container\Container;
use Illuminate\Contracts\Auth\Factory as Auth;

/**
 * Class Authenticate.
 */
class Authenticate extends IlluminateAuthenticate
{
    /**
     * @var \Illuminate\Container\Container|\LaravelBoot\Foundation\Application
     */
    protected $container;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory $auth
     * @param \Illuminate\Container\Container     $container
     */
    public function __construct(Auth $auth, Container $container)
    {
        parent::__construct($auth);
        $this->container = $container;
    }

    /**
     * @param array $guards
     *
     * @return null|void
     * @throws \Illuminate\Auth\AuthenticationException
     */
    protected function authenticate(array $guards)
    {
        if (empty($guards)) {
            return $this->auth->authenticate();
        }
        foreach ($guards as $guard) {
            if ($this->container->isInstalled() && $this->container->make('setting')->get('debug.testing', false) && $guard == 'api') {
                return null;
            }
            if ($this->auth->guard($guard)->check()) {
                return $this->auth->shouldUse($guard);
            }
        }

        throw new AuthenticationException('Unauthenticated.', $guards);
    }
}
