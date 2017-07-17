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
namespace LaravelBoot\Foundation\Database\Commands;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\SeederMakeCommand as IlluminateSeederMakeCommand;

/**
 * Class SeederMakeCommand.
 */
class SeederMakeCommand extends IlluminateSeederMakeCommand
{
    /**
     * Get stub file.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/../../../stubs/seeders/seeder.stub';
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param string $stub
     * @param string $name
     *
     * @return mixed
     */
    protected function replaceClass($stub, $name)
    {
        $stub = parent::replaceClass($stub, $name);

        return str_replace('DummyDatetime', Carbon::now()->toDateTimeString(), $stub);
    }
}
