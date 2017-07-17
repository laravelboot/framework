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
namespace LaravelBoot\Foundation\Event\Commands;

use Carbon\Carbon;
use Illuminate\Console\GeneratorCommand;

/**
 * Class EventMakeCommand.
 */
class EventMakeCommand extends GeneratorCommand
{
    /**
     * @var string
     */
    protected $name = 'make:event';

    /**
     * @var string
     */
    protected $description = 'Create a new event class';

    /**
     * @var string
     */
    protected $type = 'Event';

    /**
     * Determine if the class already exists.
     *
     * @param string $rawName
     *
     * @return bool
     */
    protected function alreadyExists($rawName)
    {
        return class_exists($rawName);
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Events';
    }

    /**
     * Get stub file.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/../../../stubs/events/event.stub';
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
