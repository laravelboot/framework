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

use Illuminate\Contracts\Config\Repository as RepositoryContract;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use LaravelBoot\Foundation\Configuration\Loaders\FileLoader;
use LaravelBoot\Foundation\Configuration\Repository;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class LoadConfiguration.
 */
class LoadConfiguration
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
        $loader = new FileLoader(new Filesystem(), $application['path'] . DIRECTORY_SEPARATOR . 'config');
        $application->instance('config', $configuration = new Repository($loader, $application->environment()));
        if (!isset($loadedFromCache)) {
            $this->loadConfigurationFiles($application, $configuration);
        }
        mb_internal_encoding('UTF-8');
    }

    /**
     * Load the configuration items from all of the files.
     *
     * @param \Illuminate\Contracts\Foundation\Application|\LaravelBoot\Foundation\Application $application
     * @param \Illuminate\Contracts\Config\Repository                                     $repository
     *
     * @return void
     */
    protected function loadConfigurationFiles(Application $application, RepositoryContract $repository)
    {
        foreach ($this->getConfigurationFiles($application) as $key => $path) {
            $repository->set($key, require $path);
        }
    }

    /**
     * Get all of the configuration files for the application.
     *
     * @param \Illuminate\Contracts\Foundation\Application|\LaravelBoot\Foundation\Application $app
     *
     * @return array
     */
    protected function getConfigurationFiles(Application $app)
    {
        $files = [];
        $configPath = realpath($app->configPath());
        foreach (Finder::create()->files()->name('*.php')->in($configPath) as $file) {
            $nesting = $this->getConfigurationNesting($file, $configPath);
            $files[$nesting . basename($file->getRealPath(), '.php')] = $file->getRealPath();
        }

        return $files;
    }

    /**
     * Get the configuration file nesting path.
     *
     * @param \Symfony\Component\Finder\SplFileInfo $file
     * @param string                                $configPath
     *
     * @return string
     */
    protected function getConfigurationNesting(SplFileInfo $file, $configPath)
    {
        $directory = dirname($file->getRealPath());
        if ($tree = trim(str_replace($configPath, '', $directory), DIRECTORY_SEPARATOR)) {
            $tree = str_replace(DIRECTORY_SEPARATOR, '.', $tree) . '.';
        }

        return $tree;
    }
}
