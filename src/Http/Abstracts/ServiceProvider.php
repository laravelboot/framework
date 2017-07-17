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
namespace LaravelBoot\Foundation\Http\Abstracts;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

/**
 * Class ServiceProvider.
 */
abstract class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application|\LaravelBoot\Foundation\Application
     */
    protected $app;

    /**
     * ServiceProvider constructor.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);
    }
}
