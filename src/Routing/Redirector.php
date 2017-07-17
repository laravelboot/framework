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
namespace LaravelBoot\Foundation\Routing;

use Illuminate\Routing\Redirector as IlluminateRedirector;
use LaravelBoot\Foundation\Http\RedirectResponse;

/**
 * Class Redirector.
 */
class Redirector extends IlluminateRedirector
{
    /**
     * Create a new redirect response.
     *
     * @param string $path
     * @param int    $status
     * @param array  $headers
     *
     * @return \LaravelBoot\Foundation\Http\RedirectResponse
     */
    public function createRedirect($path, $status, $headers)
    {
        $redirect = new RedirectResponse($path, $status, $headers);
        if (isset($this->session)) {
            $redirect->setSession($this->session);
        }
        $redirect->setRequest($this->generator->getRequest());

        return $redirect;
    }
}
