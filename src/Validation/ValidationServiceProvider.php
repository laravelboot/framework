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
namespace LaravelBoot\Foundation\Validation;

use Illuminate\Validation\ValidationServiceProvider as IlluminateValidationServiceProvider;

/**
 * Class ValidationServiceProvider.
 */
class ValidationServiceProvider extends IlluminateValidationServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = true;
}
