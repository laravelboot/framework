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
namespace LaravelBoot\Foundation\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class ConsoleMakeCommand.
 */
class ConsoleMakeCommand extends GeneratorCommand
{
    /**
     * @var string
     */
    protected $name = 'make:command';

    /**
     * @var string
     */
    protected $description = 'Create a new Artisan command';

    /**
     * @var string
     */
    protected $type = 'Console command';

    /**
     * Replace the class name for the given stub.
     *
     * @param string $stub
     * @param string $name
     *
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $stub = parent::replaceClass($stub, $name);
        $stub = str_replace('DummyDatetime', Carbon::now()->toDateTimeString(), $stub);

        return str_replace('dummy:command', $this->option('command'), $stub);
    }

    /**
     * Get stub file.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/../../../stubs/consoles/command.stub';
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
        return $rootNamespace . '\Console\Commands';
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            [
                'name',
                InputArgument::REQUIRED,
                'The name of the command.',
            ],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            [
                'command',
                null,
                InputOption::VALUE_OPTIONAL,
                'The terminal command that should be assigned.',
                'command:name',
            ],
        ];
    }
}
