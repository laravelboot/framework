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

use ErrorException;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Debug\Exception\FatalErrorException;
use Symfony\Component\Debug\Exception\FatalThrowableError;

/**
 * Class HandleExceptions.
 */
class HandleExceptions
{
    /**
     * @var \Illuminate\Contracts\Foundation\Application|\LaravelBoot\Foundation\Application
     */
    protected $app;

    /**
     * Bootstrap the given application.
     *
     * @param \Illuminate\Contracts\Foundation\Application|\LaravelBoot\Foundation\Application $application
     *
     * @return void
     */
    public function bootstrap(Application $application)
    {
        $this->app = $application;
        error_reporting(-1);
        set_error_handler([
            $this,
            'handleError',
        ]);
        set_exception_handler([
            $this,
            'handleException',
        ]);
        register_shutdown_function([
            $this,
            'handleShutdown',
        ]);
        if (!$application->environment('testing')) {
            ini_set('display_errors', 'Off');
        }
        if ($application->make('config')->get('app.debug')) {
            ini_set('display_errors', true);
        }
    }

    /**
     * Convert a PHP error to an ErrorException.
     *
     * @param int    $level
     * @param string $message
     * @param string $file
     * @param int    $line
     * @param array  $context
     *
     * @throws \ErrorException
     * @return void
     */
    public function handleError($level, $message, $file = '', $line = 0, $context = [])
    {
        if (error_reporting() & $level) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * Handle an uncaught exception from the application.
     *
     * @param \Throwable $e
     *
     * @return void
     */
    public function handleException($e)
    {
        if (!$e instanceof Exception) {
            $e = new FatalThrowableError($e);
        }
        $this->getExceptionHandler()->report($e);
        if ($this->app->runningInConsole()) {
            $this->renderForConsole($e);
        } else {
            $this->renderHttpResponse($e);
        }
    }

    /**
     * Render an exception to the console.
     *
     * @param \Exception $e
     *
     * @return void
     */
    protected function renderForConsole(Exception $e)
    {
        $this->getExceptionHandler()->renderForConsole(new ConsoleOutput(), $e);
    }

    /**
     * Render an exception as an HTTP response and send it.
     *
     * @param \Exception $e
     *
     * @return void
     */
    protected function renderHttpResponse(Exception $e)
    {
        $this->getExceptionHandler()->render($this->app['request'], $e)->send();
    }

    /**
     * Handle the PHP shutdown event.
     */
    public function handleShutdown()
    {
        if (!is_null($error = error_get_last()) && $this->isFatal($error['type'])) {
            $this->handleException($this->fatalExceptionFromError($error, 0));
        }
    }

    /**
     * Create a new fatal exception instance from an error array.
     *
     * @param array    $error
     * @param int|null $traceOffset
     *
     * @return \Symfony\Component\Debug\Exception\FatalErrorException
     */
    protected function fatalExceptionFromError(array $error, $traceOffset = null)
    {
        return new FatalErrorException($error['message'], $error['type'], 0, $error['file'], $error['line'],
            $traceOffset);
    }

    /**
     * Determine if the error type is fatal.
     *
     * @param int $type
     *
     * @return bool
     */
    protected function isFatal($type)
    {
        return in_array($type, [
            E_ERROR,
            E_CORE_ERROR,
            E_COMPILE_ERROR,
            E_PARSE,
        ]);
    }

    /**
     * Get an instance of the exception handler.
     *
     * @return \Illuminate\Contracts\Debug\ExceptionHandler
     */
    protected function getExceptionHandler()
    {
        return $this->app->make('Illuminate\Contracts\Debug\ExceptionHandler');
    }
}
