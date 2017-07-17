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
namespace LaravelBoot\Foundation\Module\Abstracts;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;
use LaravelBoot\Foundation\Http\Abstracts\ServiceProvider;

/**
 * Class Module.
 */
abstract class Module extends ServiceProvider
{
    /**
     * @var \Illuminate\Events\Dispatcher
     */
    protected $events;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected static $migrations;

    /**
     * @var \Illuminate\Routing\Router
     */
    protected $router;

    /**
     * Module constructor.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->events = $app['events'];
        $this->router = $app['router'];
        static::$migrations = new Collection();
    }

    /**
     * Boot module.
     */
    abstract public function boot();

    /**
     * Description of module
     *
     * @return string
     */
    abstract public static function description();

    /**
     * Install for module.
     *
     * @return string
     */
    abstract public static function install();

    /**
     * @param array|string $paths
     */
    public function loadMigrationsFrom($paths)
    {
        static::$migrations = static::$migrations->merge((array)$paths);
        parent::loadMigrationsFrom($paths);
    }

    public static function migrations() {
        return static::$migrations->toArray();
    }

    /**
     * Name of module.
     *
     * @return string
     */
    abstract public static function name();

    /**
     * Register module extra providers.
     */
    public function register()
    {
    }

    /**
     * Uninstall for module.
     *
     * @return string
     */
    abstract public static function uninstall();

    /**
     * Version of module.
     *
     * @return string
     */
    abstract public static function version();
}
