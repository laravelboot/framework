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
namespace LaravelBoot\Foundation\Console;

use Closure;
use Exception;
use Illuminate\Console\Events\ArtisanStarting;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Console\Kernel as KernelContract;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use LaravelBoot\Foundation\Http\Bootstraps\LoadProviders;
use LaravelBoot\Foundation\Http\Bootstraps\ConfigureLogging;
use LaravelBoot\Foundation\Http\Bootstraps\LoadEnvironmentVariables;
use LaravelBoot\Foundation\Http\Bootstraps\HandleExceptions;
use LaravelBoot\Foundation\Http\Bootstraps\LoadConfiguration;
use LaravelBoot\Foundation\Http\Bootstraps\LoadSetting;
use LaravelBoot\Foundation\Http\Bootstraps\RegisterFacades;
use LaravelBoot\Foundation\Http\Bootstraps\RegisterFlow;
use LaravelBoot\Foundation\Http\Bootstraps\RegisterPermission;
use LaravelBoot\Foundation\Http\Bootstraps\RegisterRouter;
use LaravelBoot\Foundation\Http\Bootstraps\SetRequestForConsole;
use LaravelBoot\Foundation\Console\Application as Artisan;
use LaravelBoot\Foundation\Console\Commands\ClosureCommand;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Throwable;

/**
 * Class Kernel.
 */
class Kernel implements KernelContract
{
    /**
     * @var \Illuminate\Contracts\Foundation\Application|\LaravelBoot\Foundation\Application
     */
    protected $app;

    /**
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $events;

    /**
     * @var \LaravelBoot\Foundation\Console\Application
     */
    protected $artisan;

    /**
     * @var array
     */
    protected $commands = [];

    /**
     * @var bool
     */
    protected $commandsLoaded = false;

    /**
     * The bootstrap classes for the application.
     *
     * @var array
     */
    protected $bootstrappers = [
        LoadEnvironmentVariables::class,
        LoadConfiguration::class,
        ConfigureLogging::class,
        HandleExceptions::class,
        RegisterFacades::class,
        SetRequestForConsole::class,
        LoadProviders::class,
        LoadSetting::class,
        RegisterRouter::class,
        RegisterPermission::class,
        RegisterFlow::class,
    ];

    /**
     * Kernel constructor.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @param \Illuminate\Contracts\Events\Dispatcher      $events
     */
    public function __construct(Application $app, Dispatcher $events)
    {
        if (!defined('ARTISAN_BINARY')) {
            define('ARTISAN_BINARY', 'artisan');
        }
        $this->app = $app;
        $this->events = $events;
        $this->app->booted(function () {
            $this->defineConsoleSchedule();
        });
    }

    /**
     * Define the application's command schedule.
     */
    protected function defineConsoleSchedule()
    {
        $this->app->instance(Schedule::class, $schedule = new Schedule($this->app[Cache::class]));
        $this->schedule($schedule);
    }

    /**
     * Run the console application.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    public function handle($input, $output = null)
    {
        try {
            $this->bootstrap();
            if (!$this->commandsLoaded) {
                $this->commands();
                $this->commandsLoaded = true;
            }

            return $this->getArtisan()->run($input, $output);
        } catch (Exception $e) {
            $this->reportException($e);
            $this->renderException($output, $e);

            return 1;
        } catch (Throwable $e) {
            $e = new FatalThrowableError($e);
            $this->reportException($e);
            $this->renderException($output, $e);

            return 1;
        }
    }

    /**
     * Terminate the application.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param int                                             $status
     *
     * @return void
     */
    public function terminate($input, $status)
    {
        $this->app->terminate();
    }

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
    }

    /**
     * Register the Closure based commands for the application.
     */
    protected function commands()
    {
    }

    /**
     * Register a Closure based command with the application.
     *
     * @param string  $signature
     * @param Closure $callback
     *
     * @return \LaravelBoot\Foundation\Console\Commands\ClosureCommand
     */
    public function command($signature, Closure $callback)
    {
        $command = new ClosureCommand($signature, $callback);
        $this->app['events']->listen(ArtisanStarting::class, function ($event) use ($command) {
            $event->artisan->add($command);
        });

        return $command;
    }

    /**
     * Register the given command with the console application.
     *
     * @param \Symfony\Component\Console\Command\Command $command
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function registerCommand($command)
    {
        $this->getArtisan()->add($command);
    }

    /**
     * Run an Artisan console command by name.
     *
     * @param string $command
     * @param array  $parameters
     *
     * @return int
     * @throws \Exception
     */
    public function call($command, array $parameters = [])
    {
        $this->bootstrap();
        if (!$this->commandsLoaded) {
            $this->commands();
            $this->commandsLoaded = true;
        }

        return $this->getArtisan()->call($command, $parameters);
    }

    /**
     * Queue the given console command.
     *
     * @param string $command
     * @param array  $parameters
     *
     * @return void
     */
    public function queue($command, array $parameters = [])
    {
        $this->app['Illuminate\Contracts\Queue\Queue']->push(QueuedJob::class, func_get_args());
    }

    /**
     * Get all of the commands registered with the console.
     *
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function all()
    {
        $this->bootstrap();

        return $this->getArtisan()->all();
    }

    /**
     * Get the output for the last run command.
     *
     * @return string
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function output()
    {
        $this->bootstrap();

        return $this->getArtisan()->output();
    }

    /**
     * Bootstrap the application for artisan commands.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function bootstrap()
    {
        if (!$this->app->hasBeenBootstrapped()) {
            $this->app->bootstrapWith($this->bootstrappers());
        }
        $this->app->loadDeferredProviders();
    }

    /**
     * Get the Artisan application instance.
     *
     * @return \LaravelBoot\Foundation\Console\Application
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getArtisan()
    {
        if (is_null($this->artisan)) {
            return $this->artisan = (new Artisan($this->app, $this->events,
                $this->app->version()))->resolveCommands($this->commands);
        }

        return $this->artisan;
    }

    /**
     * Set the Artisan application instance.
     *
     * @param \LaravelBoot\Foundation\Console\Application $artisan
     *
     * @return void
     */
    public function setArtisan($artisan)
    {
        $this->artisan = $artisan;
    }

    /**
     * Get the bootstrap classes for the application.
     *
     * @return array
     */
    protected function bootstrappers()
    {
        return $this->bootstrappers;
    }

    /**
     * Report the exception to the exception handler.
     *
     * @param \Exception $e
     *
     * @return void
     */
    protected function reportException(Exception $e)
    {
        $this->app['Illuminate\Contracts\Debug\ExceptionHandler']->report($e);
    }

    /**
     * Report the exception to the exception handler.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \Exception                                        $e
     *
     * @return void
     */
    protected function renderException($output, Exception $e)
    {
        $this->app['Illuminate\Contracts\Debug\ExceptionHandler']->renderForConsole($output, $e);
    }
}
