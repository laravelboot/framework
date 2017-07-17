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
namespace LaravelBoot\Foundation\Database;

use Illuminate\Database\MigrationServiceProvider as IlluminateMigrationServiceProvider;
use LaravelBoot\Foundation\Database\Migrations\DatabaseMigrationRepository;
use LaravelBoot\Foundation\Database\Migrations\MigrationCreator;
use LaravelBoot\Foundation\Database\Migrations\Migrator;

/**
 * Class MigrationServiceProvider.
 */
class MigrationServiceProvider extends IlluminateMigrationServiceProvider
{
    /**
     * Register the migration creator.
     */
    protected function registerCreator()
    {
        $this->app->singleton('migration.creator', function ($app) {
            return new MigrationCreator($app, $app['files']);
        });
    }

    /**
     * Register the migrator service.
     */
    protected function registerMigrator()
    {
        $this->app->singleton('migrator', function ($app) {
            $repository = $app['migration.repository'];

            return new Migrator($app, $repository, $app['db'], $app['files']);
        });
    }

    /**
     * Register the migration repository service.
     *
     * @return void
     */
    protected function registerRepository()
    {
        $this->app->singleton('migration.repository', function ($app) {
            $table = $app['config']['database.migrations'];

            return new DatabaseMigrationRepository($app['db'], $table);
        });
    }
}
