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
namespace LaravelBoot\Foundation\Http\Bootstraps;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Facade;
use LaravelBoot\Foundation\AliasLoader;
use LaravelBoot\Foundation\Facades\FacadeRegister;

/**
 * Class RegisterFacades.
 */
class RegisterFacades
{
    /**
     * Bootstrap the given application.
     *
     * @param \Illuminate\Contracts\Foundation\Application $application
     *
     * @return void
     */
    public function bootstrap(Application $application)
    {
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($application);
        $aliasLoader = AliasLoader::getInstance($application->make('config')->get('app.aliases', []));
        $application->make('events')->dispatch(new FacadeRegister($aliasLoader));
        $aliasLoader->register();
    }
}
