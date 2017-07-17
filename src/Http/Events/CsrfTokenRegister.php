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
namespace LaravelBoot\Foundation\Http\Events;

use LaravelBoot\Foundation\Http\Middlewares\VerifyCsrfToken;

/**
 * Class CsrfTokenRegister.
 */
class CsrfTokenRegister
{
    /**
     * @var \LaravelBoot\Foundation\Http\Middlewares\VerifyCsrfToken
     */
    protected $verifier;

    /**
     * CsrfTokenRegister constructor.
     *
     * @param \LaravelBoot\Foundation\Http\Middlewares\VerifyCsrfToken $verifier
     *
     * @internal param \Illuminate\Container\Container $container
     */
    public function __construct(VerifyCsrfToken $verifier)
    {
        $this->verifier = $verifier;
    }

    /**
     * Register except to verifier.
     *
     * @param $excepts
     */
    public function registerExcept($excepts)
    {
        $this->verifier->registerExcept($excepts);
    }
}
