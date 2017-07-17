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
namespace LaravelBoot\Foundation\Routing\Commands;

use Carbon\Carbon;
use Illuminate\Routing\Console\MiddlewareMakeCommand as IlluminateMiddlewareMakeCommand;

/**
 * Class MiddlewareMakeCommand.
 */
class MiddlewareMakeCommand extends IlluminateMiddlewareMakeCommand
{
    /**
     * Get stub file.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/../../../stubs/routes/middleware.stub';
    }

    /**
     * Replace class name by holder.
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
