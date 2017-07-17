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
namespace LaravelBoot\Foundation;

use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Filesystem\Filesystem;

/**
 * Class ProviderRepository.
 */
class ProviderRepository
{
    /**
     * @var \Illuminate\Contracts\Foundation\Application|\LaravelBoot\Foundation\Application
     */
    protected $app;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * @var string
     */
    protected $manifestPath;

    /**
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @param \Illuminate\Filesystem\Filesystem            $files
     * @param string                                       $manifestPath
     */
    public function __construct(ApplicationContract $app, Filesystem $files, $manifestPath)
    {
        $this->app = $app;
        $this->files = $files;
        $this->manifestPath = $manifestPath;
    }

    /**
     * Register the application service providers.
     *
     * @param array $providers
     *
     * @return void
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function load(array $providers)
    {
        $manifest = $this->loadManifest();
        if ($this->shouldRecompile($manifest, $providers)) {
            $manifest = $this->compileManifest($providers);
        }
        foreach ($manifest['when'] as $provider => $events) {
            $this->registerLoadEvents($provider, $events);
        }
        foreach ($manifest['eager'] as $provider) {
            $this->app->register($this->createProvider($provider));
        }
        $this->app->addDeferredServices($manifest['deferred']);
    }

    /**
     * Register the load events for the given provider.
     *
     * @param string $provider
     * @param array  $events
     *
     * @return void
     */
    protected function registerLoadEvents($provider, array $events)
    {
        if (count($events) < 1) {
            return;
        }
        $app = $this->app;
        $app->make('events')->listen($events, function () use ($app, $provider) {
            $app->register($provider);
        });
    }

    /**
     * Compile the application manifest file.
     *
     * @param array $providers
     *
     * @return array
     */
    protected function compileManifest($providers)
    {
        $manifest = $this->freshManifest($providers);
        foreach ($providers as $provider) {
            $instance = $this->createProvider($provider);
            if ($instance->isDeferred()) {
                foreach ($instance->provides() as $service) {
                    $manifest['deferred'][$service] = $provider;
                }
                $manifest['when'][$provider] = $instance->when();
            } else {
                $manifest['eager'][] = $provider;
            }
        }

        return $this->writeManifest($manifest);
    }

    /**
     * Create a new provider instance.
     *
     * @param string $provider
     *
     * @return \Illuminate\Support\ServiceProvider
     */
    public function createProvider($provider)
    {
        return new $provider($this->app);
    }

    /**
     * Determine if the manifest should be compiled.
     *
     * @param array $manifest
     * @param array $providers
     *
     * @return bool
     */
    public function shouldRecompile($manifest, $providers)
    {
        return is_null($manifest) || $manifest['providers'] != $providers;
    }

    /**
     * Load the service provider manifest JSON file.
     *
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function loadManifest()
    {
        if ($this->files->exists($this->manifestPath)) {
            $manifest = $this->files->getRequire($this->manifestPath);
            if ($manifest) {
                return array_merge(['when' => []], $manifest);
            }
        }
    }

    /**
     * Write the service manifest file to disk.
     *
     * @param $manifest
     *
     * @return array
     */
    public function writeManifest($manifest)
    {
        $this->files->put($this->manifestPath, '<?php return ' . var_export($manifest, true) . ';');

        return array_merge(['when' => []], $manifest);
    }

    /**
     * Create a fresh service manifest data structure.
     *
     * @param array $providers
     *
     * @return array
     */
    protected function freshManifest(array $providers)
    {
        return [
            'providers' => $providers,
            'eager'     => [],
            'deferred'  => [],
        ];
    }
}
