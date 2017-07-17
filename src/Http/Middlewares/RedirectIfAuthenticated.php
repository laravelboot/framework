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

use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * Class RedirectIfAuthenticated.
 */
class RedirectIfAuthenticated
{
    /**
     * Middleware handler.
     *
     * @param          $request
     * @param \Closure $next
     * @param null     $guard
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            return redirect('/');
        }

        return $next($request);
    }
}
