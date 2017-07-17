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

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Finder\Finder;

/**
 * Class AppNameCommand.
 */
class AppNameCommand extends Command
{
    /**
     * @var string
     */
    protected $name = 'app:name';

    /**
     * @var string
     */
    protected $description = 'Set the application namespace';

    /**
     * @var \Illuminate\Support\Composer
     */
    protected $composer;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * @var string
     */
    protected $currentRoot;

    /**
     * AppNameCommand constructor.
     *
     * @param \Illuminate\Support\Composer      $composer
     * @param \Illuminate\Filesystem\Filesystem $files
     */
    public function __construct(Composer $composer, Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
        $this->composer = $composer;
    }

    /**
     * Command handler.
     */
    public function fire()
    {
        $this->currentRoot = trim($this->laravel->getNamespace(), '\\');
        $this->setBootstrapNamespaces();
        $this->setAppDirectoryNamespace();
        $this->setConfigNamespaces();
        $this->setComposerNamespace();
        $this->setDatabaseFactoryNamespaces();
        $this->info('Application namespace set!');
        $this->composer->dumpAutoloads();
        $this->call('clear-compiled');
    }

    /**
     * Set the namespace on the files in the app directory.
     */
    protected function setAppDirectoryNamespace()
    {
        $files = Finder::create()->in($this->laravel['path'])->contains($this->currentRoot)->name('*.php');
        foreach ($files as $file) {
            $this->replaceNamespace($file->getRealPath());
        }
    }

    /**
     * Replace the App namespace at the given path.
     *
     * @param string $path
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function replaceNamespace($path)
    {
        $search = [
            'namespace ' . $this->currentRoot . ';',
            $this->currentRoot . '\\',
        ];
        $replace = [
            'namespace ' . $this->argument('name') . ';',
            $this->argument('name') . '\\',
        ];
        $this->replaceIn($path, $search, $replace);
    }

    /**
     * Set the bootstrap namespaces.
     */
    protected function setBootstrapNamespaces()
    {
        $search = [
            $this->currentRoot . '\\Http',
            $this->currentRoot . '\\Console',
            $this->currentRoot . '\\Exceptions',
        ];
        $replace = [
            $this->argument('name') . '\\Http',
            $this->argument('name') . '\\Console',
            $this->argument('name') . '\\Exceptions',
        ];
        $this->replaceIn($this->getBootstrapPath(), $search, $replace);
    }

    /**
     * Set the PSR-4 namespace in the Composer file.
     */
    protected function setComposerNamespace()
    {
        $this->replaceIn($this->getComposerPath(), str_replace('\\', '\\\\', $this->currentRoot) . '\\\\',
            str_replace('\\', '\\\\', $this->argument('name')) . '\\\\');
    }

    /**
     * Set the namespace in the appropriate configuration files.
     */
    protected function setConfigNamespaces()
    {
        $this->setAppConfigNamespaces();
        $this->setAuthConfigNamespace();
        $this->setServicesConfigNamespace();
    }

    /**
     * Set the application provider namespaces.
     */
    protected function setAppConfigNamespaces()
    {
        $search = [
            $this->currentRoot . '\\Providers',
            $this->currentRoot . '\\Http\\Controllers\\',
        ];
        $replace = [
            $this->argument('name') . '\\Providers',
            $this->argument('name') . '\\Http\\Controllers\\',
        ];
        $this->replaceIn($this->getConfigPath('app'), $search, $replace);
    }

    /**
     * Set the authentication User namespace.
     */
    protected function setAuthConfigNamespace()
    {
        $this->replaceIn($this->getConfigPath('auth'), $this->currentRoot . '\\User',
            $this->argument('name') . '\\User');
    }

    /**
     * Set the services User namespace.
     */
    protected function setServicesConfigNamespace()
    {
        $this->replaceIn($this->getConfigPath('services'), $this->currentRoot . '\\User',
            $this->argument('name') . '\\User');
    }

    /**
     * Set the namespace in database factory files.
     */
    protected function setDatabaseFactoryNamespaces()
    {
        $this->replaceIn($this->laravel->databasePath() . '/factories/ModelFactory.php', $this->currentRoot,
            $this->argument('name'));
    }

    /**
     * Replace the given string in the given file.
     *
     * @param string       $path
     * @param string|array $search
     * @param string|array $replace
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function replaceIn($path, $search, $replace)
    {
        $this->files->put($path, str_replace($search, $replace, $this->files->get($path)));
    }

    /**
     * Get the path to the bootstrap/app.php file.
     *
     * @return string
     */
    protected function getBootstrapPath()
    {
        return $this->laravel->bootstrapPath() . '/app.php';
    }

    /**
     * Get the path to the Composer.json file.
     *
     * @return string
     */
    protected function getComposerPath()
    {
        return $this->laravel->basePath() . '/composer.json';
    }

    /**
     * Get the path to the given configuration file.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getConfigPath($name)
    {
        return $this->laravel['path.config'] . '/' . $name . '.php';
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
                'The desired namespace.',
            ],
        ];
    }
}
