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

use Carbon\Carbon;
use Illuminate\Database\Migrations\MigrationCreator as IlluminateMigrationCreator;
use Illuminate\Filesystem\Filesystem;
use LaravelBoot\Foundation\Application;

/**
 * Class MigrationCreator.
 */
class MigrationCreator extends IlluminateMigrationCreator
{
    /**
     * @var \LaravelBoot\Foundation\Application
     */
    protected $application;

    /**
     * MigrationCreator constructor.
     *
     * @param \LaravelBoot\Foundation\Application    $application
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     */
    public function __construct(Application $application, Filesystem $filesystem)
    {
        parent::__construct($filesystem);
        $this->application = $application;
    }

    /**
     * Get the path to the stubs.
     *
     * @return string
     */
    public function getStubPath()
    {
        return __DIR__ . '/../../../stubs/migrations';
    }

    /**
     * Populate the place-holders in the migration stub.
     *
     * @param string $stub
     * @param string $name
     *
     * @return mixed
     */
    protected function populateStub($name, $stub, $table)
    {
        $stub = parent::populateStub($name, $stub, $table);

        return str_replace('DummyDatetime', Carbon::now()->toDateTimeString(), $stub);
    }

    /**
     * Get the path to the stubs.
     *
     * @return string
     */
    public function stubPath()
    {
        return __DIR__ . '/../../../stubs/migrations';
    }
}
