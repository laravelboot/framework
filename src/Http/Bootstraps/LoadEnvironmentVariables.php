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
use LaravelBoot\Foundation\Yaml\Exceptions\InvalidPathException;
use LaravelBoot\Foundation\Yaml\YamlEnv;
use Symfony\Component\Console\Input\ArgvInput;

/**
 * Class DetectEnvironment.
 */
class LoadEnvironmentVariables
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
        $application->singleton('yaml.environment', function () use ($application) {
            return new YamlEnv($application->environmentPath(), $application->environmentFile());
        });
        if (!$application->configurationIsCached()) {
            $this->checkForSpecificEnvironmentFile($application);
            try {
                $application->make('yaml.environment')->load();
            } catch (InvalidPathException $e) {
            }
        }
        $application->detectEnvironment(function () {
            return env('APP_ENV', 'production');
        });
    }

    /**
     * Detect if a custom environment file matching the APP_ENV exists.
     *
     * @param \Illuminate\Contracts\Foundation\Application|\LaravelBoot\Foundation\Application $app
     *
     * @return void
     */
    protected function checkForSpecificEnvironmentFile($app)
    {
        if (php_sapi_name() == 'cli') {
            $input = new ArgvInput();
            if ($input->hasParameterOption('--env')) {
                $file = $app->environmentFile() . '.' . $input->getParameterOption('--env');
                $this->loadEnvironmentFile($app, $file);
            }
        }
        if (!env('APP_ENV')) {
            return;
        }
        if (empty($file)) {
            $file = $app->environmentFile() . '.' . env('APP_ENV');
            $this->loadEnvironmentFile($app, $file);
        }
    }

    /**
     * Load a custom environment file.
     *
     * @param \Illuminate\Contracts\Foundation\Application|\LaravelBoot\Foundation\Application $app
     * @param string                                                                      $file
     *
     * @return void
     */
    protected function loadEnvironmentFile($app, $file)
    {
        if (file_exists($app->environmentPath() . '/' . $file)) {
            $app->loadEnvironmentFrom($file);
        }
    }
}
