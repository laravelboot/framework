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
namespace LaravelBoot\Foundation\Http\Commands;

use Carbon\Carbon;
use Illuminate\Console\GeneratorCommand;

/**
 * Class RequestMakeCommand.
 */
class RequestMakeCommand extends GeneratorCommand
{
    /**
     * @var string
     */
    protected $name = 'make:request';

    /**
     * @var string
     */
    protected $description = 'Create a new form request class';

    /**
     * @var string
     */
    protected $type = 'Request';

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Http\Requests';
    }

    /**
     * Get stub file.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/../../../stubs/requests/request.stub';
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
