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
namespace LaravelBoot\Foundation\Http;

use Illuminate\Http\RedirectResponse as IlluminateRedirectResponse;
use Illuminate\Support\ViewErrorBag;

/**
 * Class RedirectResponse.
 */
class RedirectResponse extends IlluminateRedirectResponse
{
    /**
     * Return messages to redirect response.
     *
     * @param \Illuminate\Contracts\Support\MessageProvider|array|string $provider
     * @param string                                                     $key
     *
     * @return $this
     */
    public function withMessages($provider, $key = 'default')
    {
        $value = $this->parseErrors($provider);
        $this->session->flash(
            'messages', $this->session->get('messages', new ViewErrorBag)->put($key, $value)
        );

        return $this;
    }
}
