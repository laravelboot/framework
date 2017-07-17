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

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Log\Writer;
use Monolog\Logger as Monolog;

/**
 * Class ConfigureLogging.
 */
class ConfigureLogging
{
    /**
     * Bootstrap the given application.
     *
     * @param \Illuminate\Contracts\Foundation\Application|\LaravelBoot\Foundation\Application $application
     *
     * @return void
     */
    public function bootstrap(Application $application)
    {
        $log = $this->registerLogger($application);
        if ($application->hasMonologConfigurator()) {
            call_user_func($application->getMonologConfigurator(), $log->getMonolog());
        } else {
            $this->configureHandlers($application, $log);
        }
    }

    /**
     * Register the logger instance in the container.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return \Illuminate\Log\Writer
     */
    protected function registerLogger(Application $app)
    {
        $app->instance('log', $log = new Writer(new Monolog($app->environment()), $app['events']));

        return $log;
    }

    /**
     * Configure the Monolog handlers for the application.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @param \Illuminate\Log\Writer                       $log
     *
     * @return void
     */
    protected function configureHandlers(Application $app, Writer $log)
    {
        $method = 'configure' . ucfirst($app['config']['app.log']) . 'Handler';
        $this->{$method}($app, $log);
    }

    /**
     * Configure the Monolog handlers for the application.
     *
     * @param \Illuminate\Contracts\Foundation\Application|\LaravelBoot\Foundation\Application $app
     * @param \Illuminate\Log\Writer                                                      $log
     *
     * @return void
     */
    protected function configureSingleHandler(Application $app, Writer $log)
    {
        $filename = $app->runningInConsole() ? 'laravelboot-console.log' : 'laravelboot-web';
        $log->useFiles($app->storagePath() . '/logs/'.$filename, $app->make('config')->get('app.log_level', 'debug'));
    }

    /**
     * Configure the Monolog handlers for the application.
     *
     * @param \Illuminate\Contracts\Foundation\Application|\LaravelBoot\Foundation\Application $app
     * @param \Illuminate\Log\Writer                                                      $log
     *
     * @return void
     */
    protected function configureDailyHandler(Application $app, Writer $log)
    {
        $config = $app->make('config');
        $maxFiles = $config->get('app.log_max_files');
        $filename = $app->runningInConsole() ? 'laravelboot-console.log' : 'laravelboot-web';
        $log->useDailyFiles($app->storagePath() . '/logs/'.$filename, is_null($maxFiles) ? 5 : $maxFiles,
            $config->get('app.log_level', 'debug'));
    }

    /**
     * Configure the Monolog handlers for the application.
     *
     * @param \Illuminate\Contracts\Foundation\Application|\LaravelBoot\Foundation\Application $app
     * @param \Illuminate\Log\Writer                                                      $log
     *
     * @return void
     */
    protected function configureSyslogHandler(Application $app, Writer $log)
    {
        $log->useSyslog('laravelboot', $app->make('config')->get('app.log_level', 'debug'));
    }

    /**
     * Configure the Monolog handlers for the application.
     *
     * @param \Illuminate\Contracts\Foundation\Application|\LaravelBoot\Foundation\Application $app
     * @param \Illuminate\Log\Writer                                                      $log
     *
     * @return void
     */
    protected function configureErrorlogHandler(Application $app, Writer $log)
    {
        $log->useErrorLog($app->make('config')->get('app.log_level', 'debug'));
    }
}
