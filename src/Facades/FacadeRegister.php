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
namespace LaravelBoot\Foundation\Facades;

use Illuminate\Container\Container;
use LaravelBoot\Foundation\AliasLoader;

/**
 * Class FacadeRegister.
 */
class FacadeRegister
{
    /**
     * @var \LaravelBoot\Foundation\AliasLoader
     */
    protected $aliasLoader;

    /**
     * FacadeRegister constructor.
     *
     * @param \LaravelBoot\Foundation\AliasLoader $aliasLoader
     *
     * @internal param \Illuminate\Container\Container|\Illuminate\Contracts\Foundation\Application $container
     */
    public function __construct(AliasLoader $aliasLoader)
    {
        $this->aliasLoader = $aliasLoader;
    }

    /**
     * @param $key
     * @param $path
     */
    public function register($key, $path) {
        $this->aliasLoader->alias($path, $key);
    }
}
