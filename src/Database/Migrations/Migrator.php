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
namespace LaravelBoot\Foundation\Database\Migrations;

use Illuminate\Container\Container;
use Illuminate\Database\ConnectionResolverInterface as Resolver;
use Illuminate\Database\Migrations\MigrationRepositoryInterface;
use Illuminate\Database\Migrations\Migrator as IlluminateMigrator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Class Migrator.
 */
class Migrator extends IlluminateMigrator
{
    /**
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * @var \LaravelBoot\Foundation\Database\Migrations\DatabaseMigrationRepository
     */
    protected $repository;

    /**
     * Migrator constructor.
     *
     * @param \Illuminate\Container\Container                              $container
     * @param \Illuminate\Database\Migrations\MigrationRepositoryInterface $repository
     * @param \Illuminate\Database\ConnectionResolverInterface             $resolver
     * @param \Illuminate\Filesystem\Filesystem                            $files
     */
    public function __construct(
        Container $container,
        MigrationRepositoryInterface $repository,
        Resolver $resolver,
        Filesystem $files
    )
    {
        $this->container = $container;
        parent::__construct($repository, $resolver, $files);
    }

    /**
     * Get the migrations for a rollback operation.
     *
     * @param array $options
     * @param array $paths
     *
     * @return array
     */
    protected function getMigrationsForRollbackOnPaths(array $options, array $paths)
    {
        if (($steps = Arr::get($options, 'step', 0)) > 0) {
            return $this->repository->getMigrations($steps);
        } else {
            return $this->repository->getLastOnPath(array_keys($this->getMigrationFiles($paths)));
        }
    }

    /**
     * Rollback the last migration operation.
     *
     * @param  array|string $paths
     * @param  array        $options
     *
     * @return array
     */
    public function rollback($paths = [], array $options = [])
    {
        $this->notes = [];
        if ($paths) {
            $migrations = $this->getMigrationsForRollbackOnPaths($options, $paths);
        } else {
            $migrations = $this->getMigrationsForRollback($options);
        }

        if (count($migrations) === 0) {
            $this->note('<info>Nothing to rollback.</info>');

            return [];
        } else {
            return $this->rollbackMigrations($migrations, $paths, $options);
        }
    }

    /**
     * Resolve migration file.
     *
     * @param string $file
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function resolve($file)
    {
        $class = Str::studly(implode('_', array_slice(explode('_', $file), 4)));

        return $this->container->make($class);
    }
}
