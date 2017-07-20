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

use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\View\ViewServiceProvider;
use RuntimeException;
use Closure;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

use LaravelBoot\Foundation\Http\Bootstraps\LoadEnvironmentVariables;
use LaravelBoot\Foundation\Event\EventServiceProvider;
use LaravelBoot\Foundation\Routing\RoutingServiceProvider;
use LaravelBoot\Foundation\Translation\Events\LocaleUpdated;

class Application extends Container implements ApplicationContract,HttpKernelInterface
{
	const VERSION = '0.1.0';

	protected $appName;

	/**
	 * @var string
	 */
	protected $basePath;

	/**
	 * @var bool
	 */
	protected $hasBeenBootstrapped = false;

	/**
	 * @var bool
	 */
	protected $booted = false;

	/**
	 * @var array
	 */
	protected $bootingCallbacks = [];

	/**
	 * @var array
	 */
	protected $bootedCallbacks = [];

	/**
	 * @var array
	 */
	protected $terminatingCallbacks = [];

	/**
	 * @var array
	 */
	protected $serviceProviders = [];

	/**
	 * @var array
	 */
	protected $loadedProviders = [];

	/**
	 * @var array
	 */
	protected $deferredServices = [];

	/**
	 * @var callable|null
	 */
	protected $monologConfigurator;

	/**
	 * @var string
	 */
	protected $databasePath;

	/**
	 * @var string
	 */
	protected $publicPath;

	/**
	 * @var string
	 */
	protected $resourcePath;

	/**
	 * @var string
	 */
	protected $storagePath;

	/**
	 * @var string
	 */
	protected $environmentPath;

	/**
	 * @var string
	 */
	protected $environmentFile = '.env';

	/**
	 * @var string
	 */
	protected $namespace = null;

	/**
	 * @param string|null $basePath
	 * @param string $appName
	 */
	public function __construct($basePath = null,$appName='laravelboot')
	{
		$this->appName = $appName;
		$this->registerBaseBindings();
		$this->registerBaseServiceProviders();
		$this->registerCoreContainerAliases();
		if ($basePath) {
			$this->setBasePath(realpath($basePath));
		}
	}

	public function getName()
	{
		return $this->appName;
	}

	/**
	 * Get the version number of the application.
	 *
	 * @return string
	 */
	public function version()
	{
		return static::VERSION;
	}

	/**
	 * Register the basic bindings into the container.
	 */
	protected function registerBaseBindings()
	{
		static::setInstance($this);
		$this->instance('app', $this);
		$this->instance('Illuminate\Container\Container', $this);
	}

	/**
	 * Register all of the base service providers.
	 */
	protected function registerBaseServiceProviders()
	{
		$this->register(new EventServiceProvider($this));
		$this->register(new FilesystemServiceProvider($this));
		$this->register(new RoutingServiceProvider($this));
		$this->register(new ViewServiceProvider($this));
	}

	/**
	 * Run the given array of bootstrap classes.
	 *
	 * @param array $bootstrappers
	 *
	 * @throws \Illuminate\Contracts\Container\BindingResolutionException
	 */
	public function bootstrapWith(array $bootstrappers)
	{
		$this->hasBeenBootstrapped = true;
		foreach ($bootstrappers as $bootstrapper) {
			$this['events']->dispatch('bootstrapping: ' . $bootstrapper, [$this]);
			$this->make($bootstrapper)->bootstrap($this);
			$this['events']->dispatch('bootstrapped: ' . $bootstrapper, [$this]);
		}
	}

	/**
	 * Register a callback to run after loading the environment.
	 *
	 * @param \Closure $callback
	 */
	public function afterLoadingEnvironment(Closure $callback)
	{
		return $this->afterBootstrapping(LoadEnvironmentVariables::class, $callback);
	}

	/**
	 * Register a callback to run before a bootstrapper.
	 *
	 * @param          $bootstrapper
	 * @param \Closure $callback
	 */
	public function beforeBootstrapping($bootstrapper, Closure $callback)
	{
		$this['events']->listen('bootstrapping: ' . $bootstrapper, $callback);
	}

	/**
	 * Register a callback to run after a bootstrapper.
	 *
	 * @param          $bootstrapper
	 * @param \Closure $callback
	 */
	public function afterBootstrapping($bootstrapper, Closure $callback)
	{
		$this['events']->listen('bootstrapped: ' . $bootstrapper, $callback);
	}

	/**
	 * Determine if the application has been bootstrapped before.
	 *
	 * @return bool
	 */
	public function hasBeenBootstrapped()
	{
		return $this->hasBeenBootstrapped;
	}

	/**
	 * Set the base path for the application.
	 *
	 * @param $basePath
	 *
	 * @return $this
	 */
	public function setBasePath($basePath)
	{
		$this->basePath = rtrim($basePath, '\/');
		$this->bindPathsInContainer();

		return $this;
	}

	/**
	 * Bind all of the application paths in the container.
	 */
	protected function bindPathsInContainer()
	{
		$this->instance('path', $this->path());
		$this->instance('path.base', $this->basePath());
		$this->instance('path.lang', $this->langPath());
		$this->instance('path.config', $this->configPath());
		$this->instance('path.public', $this->publicPath());
		$this->instance('path.storage', $this->storagePath());
		$this->instance('path.database', $this->databasePath());
		$this->instance('path.resources', $this->resourcePath());
		$this->instance('path.bootstrap', $this->bootstrapPath());
	}

	/**
	 * Get the path to the application "app" directory.
	 *
	 * @return string
	 */
	public function path()
	{
		return $this->basePath . DIRECTORY_SEPARATOR . 'src';
	}

	/**
	 * Get the base path of the Laravel installation.
	 *
	 * @return string
	 */
	public function basePath()
	{
		return $this->basePath;
	}

	/**
	 * Get the path to the bootstrap directory.
	 *
	 * @return string
	 */
	public function bootstrapPath()
	{
		return $this->storagePath() . DIRECTORY_SEPARATOR . 'bootstraps';
	}

	/**
	 * Get the path to the application configuration files.
	 *
	 * @return string
	 */
	public function configPath()
	{
		return realpath(__DIR__ . '/../config');
	}

	/**
	 * Get the path to the database directory.
	 *
	 * @return string
	 */
	public function databasePath()
	{
		return $this->databasePath ?: $this->basePath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'databases';
	}

	/**
	 * Set the database directory.
	 *
	 * @param $path
	 *
	 * @return $this
	 */
	public function useDatabasePath($path)
	{
		$this->databasePath = $path;
		$this->instance('path.database', $path);

		return $this;
	}

	/**
	 * Get the path to the language files.
	 *
	 * @return string
	 */
	public function langPath()
	{
		return $this->resourcePath() . DIRECTORY_SEPARATOR . 'translations';
	}

	/**
	 * Get the path to the public / web directory.
	 *
	 * @return string
	 */
	public function publicPath()
	{
		return $this->publicPath ?: $this->basePath . DIRECTORY_SEPARATOR . 'public';
	}

	/**
	 * Set the path to the public / web directory.
	 *
	 * @param $path
	 *
	 * @return $this
	 */
	public function usePublicPath($path)
	{
		$this->publicPath = $path;
		$this->instance('path.public', $path);

		return $this;
	}

	/**
	 * Get the path to the storage directory.
	 *
	 * @return string
	 */
	public function storagePath()
	{
		return $this->storagePath ?: $this->basePath . DIRECTORY_SEPARATOR . 'storage';
	}

	/**
	 * Set the storage directory.
	 *
	 * @param $path
	 *
	 * @return $this
	 */
	public function useStoragePath($path)
	{
		$this->storagePath = $path;
		$this->instance('path.storage', $path);

		return $this;
	}

	/**
	 * Get the path to the resources directory.
	 *
	 * @return string
	 */
	public function resourcePath()
	{
		return $this->resourcePath ?: realpath(__DIR__ . '/../resources');
	}

	/**
	 * Set the resource directory.
	 *
	 * @param $path
	 *
	 * @return $this
	 */
	public function useResourcePath($path)
	{
		$this->resourcePath = $path;
		$this->instance('path.resources', $path);

		return $this;
	}

	/**
	 * Get the path to the environment file directory.
	 *
	 * @return string
	 */
	public function environmentPath()
	{
		return $this->environmentPath ?: $this->storagePath() . DIRECTORY_SEPARATOR . 'environments';
	}

	/**
	 * Set the directory for the environment file.
	 *
	 * @param $path
	 *
	 * @return $this
	 */
	public function useEnvironmentPath($path)
	{
		$this->environmentPath = $path;

		return $this;
	}

	/**
	 * Set the environment file to be loaded during bootstrapping.
	 *
	 * @param $file
	 *
	 * @return $this
	 */
	public function loadEnvironmentFrom($file)
	{
		$this->environmentFile = $file;

		return $this;
	}

	/**
	 * Get the environment file the application is using.
	 *
	 * @return string
	 */
	public function environmentFile()
	{
		return $this->environmentFile ?: '.env';
	}

	/**
	 * Get the fully qualified path to the environment file.
	 *
	 * @return string
	 */
	public function environmentFilePath()
	{
		return $this->environmentPath() . DIRECTORY_SEPARATOR . $this->environmentFile();
	}

	/**
	 * Get or check the current application environment.
	 *
	 * @return bool|mixed
	 */
	public function environment()
	{
		if (func_num_args() > 0) {
			$patterns = is_array(func_get_arg(0)) ? func_get_arg(0) : func_get_args();
			foreach ($patterns as $pattern) {
				if (Str::is($pattern, $this['env'])) {
					return true;
				}
			}

			return false;
		}

		return $this['env'];
	}

	/**
	 * Determine if application is in local environment.
	 *
	 * @return bool
	 */
	public function isLocal()
	{
		return $this['env'] == 'local';
	}

	/**
	 * Detect the application's current environment.
	 *
	 * @param \Closure $callback
	 *
	 * @return string
	 */
	public function detectEnvironment(Closure $callback)
	{
		$args = isset($_SERVER['argv']) ? $_SERVER['argv'] : null;

		return $this['env'] = (new EnvironmentDetector())->detect($callback, $args);
	}

	/**
	 * Determine if we are running in the console.
	 *
	 * @return bool
	 */
	public function runningInConsole()
	{
		return php_sapi_name() == 'cli';
	}

	/**
	 * Determine if we are running unit tests.
	 *
	 * @return bool
	 */
	public function runningUnitTests()
	{
		return $this['env'] == 'testing';
	}

	/**
	 * Register all of the configured providers.
	 */
	public function registerConfiguredProviders()
	{
		$manifestPath = $this->getCachedServicesPath();
		(new ProviderRepository($this, new Filesystem(), $manifestPath))->load($this['config']->get('app.providers'));
	}

	/**
	 * Register a service provider with the application.
	 *
	 * @param \Illuminate\Support\ServiceProvider|string $provider
	 * @param array                                      $options
	 * @param bool                                       $force
	 *
	 * @return \Illuminate\Support\ServiceProvider
	 */
	public function register($provider, $options = [], $force = false)
	{
		if (($registered = $this->getProvider($provider)) && !$force) {
			return $registered;
		}
		if (is_string($provider)) {
			$provider = $this->resolveProviderClass($provider);
		}
		if (method_exists($provider, 'register')) {
			$provider->register();
		}
		foreach ($options as $key => $value) {
			$this[ $key ] = $value;
		}
		$this->markAsRegistered($provider);
		if ($this->booted) {
			$this->bootProvider($provider);
		}

		return $provider;
	}

	/**
	 * Get the registered service provider instance if it exists.
	 *
	 * @param \Illuminate\Support\ServiceProvider|string $provider
	 *
	 * @return \Illuminate\Support\ServiceProvider|null
	 */
	public function getProvider($provider)
	{
		$name = is_string($provider) ? $provider : get_class($provider);

		return Arr::first($this->serviceProviders, function ($value) use ($name) {
			return $value instanceof $name;
		});
	}

	/**
	 * Resolve a service provider instance from the class name.
	 *
	 * @param string $provider
	 *
	 * @return \Illuminate\Support\ServiceProvider
	 */
	public function resolveProviderClass($provider)
	{
		return new $provider($this);
	}

	/**
	 * Mark the given provider as registered.
	 *
	 * @param \Illuminate\Support\ServiceProvider $provider
	 *
	 * @return void
	 */
	protected function markAsRegistered($provider)
	{
		$this['events']->dispatch($class = get_class($provider), [$provider]);
		$this->serviceProviders[] = $provider;
		$this->loadedProviders[ $class ] = true;
	}

	/**
	 * Load and boot all of the remaining deferred providers.
	 *
	 * @return void
	 */
	public function loadDeferredProviders()
	{
		foreach ($this->deferredServices as $service => $provider) {
			$this->loadDeferredProvider($service);
		}
		$this->deferredServices = [];
	}

	/**
	 * Load the provider for a deferred service.
	 *
	 * @param $service
	 */
	public function loadDeferredProvider($service)
	{
		if (!isset($this->deferredServices[ $service ])) {
			return;
		}
		$provider = $this->deferredServices[ $service ];
		if (!isset($this->loadedProviders[ $provider ])) {
			$this->registerDeferredProvider($provider, $service);
		}
	}

	/**
	 * Register a deferred provider and service.
	 *
	 * @param string $provider
	 * @param null   $service
	 */
	public function registerDeferredProvider($provider, $service = null)
	{
		if ($service) {
			unset($this->deferredServices[ $service ]);
		}
		$this->register($instance = new $provider($this));
		if (!$this->booted) {
			$this->booting(function () use ($instance) {
				$this->bootProvider($instance);
			});
		}
	}

	/**
	 * Resolve the given type from the container.
	 *
	 * @param string $abstract
	 * @param array  $parameters
	 *
	 * @return mixed
	 * @throws \Illuminate\Contracts\Container\BindingResolutionException
	 */
	public function make($abstract, array $parameters = [])
	{
		$abstract = $this->getAlias($abstract);
		if (isset($this->deferredServices[ $abstract ])) {
			$this->loadDeferredProvider($abstract);
		}

		return parent::make($abstract, $parameters);
	}

	/**
	 * Check a instance is bound or not.
	 *
	 * @param string $abstract
	 *
	 * @return bool
	 */
	public function bound($abstract)
	{
		return isset($this->deferredServices[ $abstract ]) || parent::bound($abstract);
	}

	/**
	 * Is Application Booted.
	 *
	 * @return bool
	 */
	public function isBooted()
	{
		return $this->booted;
	}

	/**
	 * Boot Application.
	 */
	public function boot()
	{
		if ($this->booted) {
			return;
		}
		$this->fireAppCallbacks($this->bootingCallbacks);
		array_walk($this->serviceProviders, function ($p) {
			$this->bootProvider($p);
		});
		$this->booted = true;
		$this->fireAppCallbacks($this->bootedCallbacks);
	}

	/**
	 * Boot a provider.
	 *
	 * @param \Illuminate\Support\ServiceProvider $provider
	 *
	 * @return mixed
	 */
	protected function bootProvider(ServiceProvider $provider)
	{
		if (method_exists($provider, 'boot')) {
			return $this->call([
				$provider,
				'boot',
			]);
		}
	}

	/**
	 * Register a new boot listener.
	 *
	 * @param mixed $callback
	 */
	public function booting($callback)
	{
		$this->bootingCallbacks[] = $callback;
	}

	/**
	 * Register a new "booted" listener.
	 *
	 * @param mixed $callback
	 */
	public function booted($callback)
	{
		$this->bootedCallbacks[] = $callback;
		if ($this->isBooted()) {
			$this->fireAppCallbacks([$callback]);
		}
	}

	/**
	 * Call the booting callbacks for the application.
	 *
	 * @param array $callbacks
	 */
	protected function fireAppCallbacks(array $callbacks)
	{
		foreach ($callbacks as $callback) {
			call_user_func($callback, $this);
		}
	}

	/**
	 * Handle a request.
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int                                       $type
	 * @param bool                                      $catch
	 *
	 * @return mixed
	 */
	public function handle(SymfonyRequest $request, $type = self::MASTER_REQUEST, $catch = true)
	{
		return $this['Illuminate\Contracts\Http\Kernel']->handle(Request::createFromBase($request));
	}

	/**
	 * Determine if middleware has been disabled for the application.
	 *
	 * @return bool
	 * @throws \Illuminate\Contracts\Container\BindingResolutionException
	 */
	public function shouldSkipMiddleware()
	{
		return $this->bound('middleware.disable') && $this->make('middleware.disable') === true;
	}

	/**
	 * Determine if the application configuration is cached.
	 *
	 * @return bool
	 */
	public function configurationIsCached()
	{
		return file_exists($this->getCachedConfigPath());
	}

	/**
	 * Get the path to the configuration cache file.
	 *
	 * @return string
	 */
	public function getCachedConfigPath()
	{
		return $this->storagePath() . '/bootstraps/cache/config.php';
	}

	/**
	 * Determine if the application routes are cached.
	 *
	 * @return mixed
	 */
	public function routesAreCached()
	{
		return $this['files']->exists($this->getCachedRoutesPath());
	}

	/**
	 * Get the path to the routes cache file.
	 *
	 * @return string
	 */
	public function getCachedRoutesPath()
	{
		return $this->storagePath() . '/bootstraps/cache/routes.php';
	}

	/**
	 * Get the path to the cached "compiled.php" file.
	 *
	 * @return string
	 */
	public function getCachedCompilePath()
	{
		return $this->storagePath() . '/bootstraps/cache/compiled.php';
	}

	/**
	 * Get the path to the cached services.php file.
	 *
	 * @return string
	 */
	public function getCachedServicesPath()
	{
		return $this->storagePath() . '/bootstraps/cache/services.php';
	}

	/**
	 * Determine if the application is currently down for maintenance.
	 *
	 * @return bool
	 */
	public function isDownForMaintenance()
	{
		return file_exists($this->storagePath() . '/bootstraps/down');
	}

	/**
	 * Throw an HttpException with the given data.
	 *
	 * @param int    $code
	 * @param string $message
	 * @param array  $headers
	 *
	 * @throws \Symfony\Component\HttpKernel\Exception\HttpException
	 * @return void
	 */
	public function abort($code, $message = '', array $headers = [])
	{
		if ($code == 404) {
			throw new NotFoundHttpException($message);
		}
		throw new HttpException($code, $message, null, $headers);
	}

	/**
	 * Register a terminating callback with the application.
	 *
	 * @param \Closure $callback
	 *
	 * @return $this
	 */
	public function terminating(Closure $callback)
	{
		$this->terminatingCallbacks[] = $callback;

		return $this;
	}

	/**
	 * Terminate the application.
	 */
	public function terminate()
	{
		foreach ($this->terminatingCallbacks as $terminating) {
			$this->call($terminating);
		}
	}

	/**
	 * Get the service providers that have been loaded.
	 *
	 * @return array
	 */
	public function getLoadedProviders()
	{
		return $this->loadedProviders;
	}

	/**
	 * Get the application's deferred services.
	 *
	 * @return array
	 */
	public function getDeferredServices()
	{
		return $this->deferredServices;
	}

	/**
	 * Set the application's deferred services.
	 *
	 * @param array $services
	 *
	 * @return void
	 */
	public function setDeferredServices(array $services)
	{
		$this->deferredServices = $services;
	}

	/**
	 * Add an array of services to the application's deferred services.
	 *
	 * @param array $services
	 *
	 * @return void
	 */
	public function addDeferredServices(array $services)
	{
		$this->deferredServices = array_merge($this->deferredServices, $services);
	}

	/**
	 * Determine if the given service is a deferred service.
	 *
	 * @param string $service
	 *
	 * @return bool
	 */
	public function isDeferredService($service)
	{
		return isset($this->deferredServices[ $service ]);
	}

	/**
	 * Define a callback to be used to configure Monolog.
	 *
	 * @param callable $callback
	 *
	 * @return $this
	 */
	public function configureMonologUsing(callable $callback)
	{
		$this->monologConfigurator = $callback;

		return $this;
	}

	/**
	 * Determine if the application has a custom Monolog configurator.
	 *
	 * @return bool
	 */
	public function hasMonologConfigurator()
	{
		return !is_null($this->monologConfigurator);
	}

	/**
	 * Get the custom Monolog configurator for the application.
	 *
	 * @return callable
	 */
	public function getMonologConfigurator()
	{
		return $this->monologConfigurator;
	}

	/**
	 * Get the current application locale.
	 *
	 * @return string
	 */
	public function getLocale()
	{
		return $this['config']->get('app.locale');
	}

	/**
	 * Set the current application locale.
	 *
	 * @param string $locale
	 *
	 * @return void
	 */
	public function setLocale($locale)
	{
		$this['config']->set('app.locale', $locale);
		$this['translator']->setLocale($locale);
		$this['events']->dispatch(LocaleUpdated::class, [$locale]);
	}

	/**
	 * Determine if application locale is the given locale.
	 *
	 * @param string $locale
	 *
	 * @return bool
	 */
	public function isLocale($locale)
	{
		return $this->getLocale() == $locale;
	}

	/**
	 * Register the core class aliases in the container.
	 */
	public function registerCoreContainerAliases()
	{
		$aliases = [
			//'administration'            => [\LaravelBoot\Foundation\Administration\Administration::class],
			'app'                       => [
				'Illuminate\Contracts\Container\Container',
				'Illuminate\Contracts\Foundation\Application',
				'LaravelBoot\Foundation\Application',
			],
			'auth'                      => [
				'Illuminate\Auth\AuthManager',
				'Illuminate\Contracts\Auth\Factory',
			],
			'blade.compiler'            => ['Illuminate\View\Compilers\BladeCompiler'],
			'cache'                     => [
				'Illuminate\Cache\CacheManager',
				'Illuminate\Contracts\Cache\Factory',
			],
			'cache.store'               => [
				'Illuminate\Cache\Repository',
				'Illuminate\Contracts\Cache\Repository',
			],
			'config'                    => [
				'Illuminate\Contracts\Config\Repository',
				'LaravelBoot\Foundation\Configuration\Repository',
			],
			'cookie'                    => [
				'Illuminate\Cookie\CookieJar',
				'Illuminate\Contracts\Cookie\Factory',
				'Illuminate\Contracts\Cookie\QueueingFactory',
			],
			'db'                        => ['Illuminate\Database\DatabaseManager'],
			'db.connection'             => [
				'Illuminate\Database\Connection',
				'Illuminate\Database\ConnectionInterface',
			],
			'encrypter'                 => [
				'Illuminate\Encryption\Encrypter',
				'Illuminate\Contracts\Encryption\Encrypter',
			],
			//'extension'                 => [\LaravelBoot\Foundation\Extension\ExtensionManager::class],
			'events'                    => [
				'Illuminate\Events\Dispatcher',
				'Illuminate\Contracts\Events\Dispatcher',
			],
			'files'                     => ['Illuminate\Filesystem\Filesystem'],
			'filesystem'                => [
				'Illuminate\Filesystem\FilesystemManager',
				'Illuminate\Contracts\Filesystem\Factory',
			],
			'filesystem.disk'           => ['Illuminate\Contracts\Filesystem\Filesystem'],
			'filesystem.cloud'          => ['Illuminate\Contracts\Filesystem\Cloud'],
			'flow'                      => ['LaravelBoot\Foundation\Flow\FlowManager'],
			'hash'                      => ['Illuminate\Contracts\Hashing\Hasher'],
			//'images'                    => [\LaravelBoot\Foundation\Image\ImageManager::class],
			'log'                       => [
				'Illuminate\Log\Writer',
				'Illuminate\Contracts\Logging\Log',
				'Psr\Log\LoggerInterface',
			],
			'mailer'                    => [
				'Illuminate\Mail\Mailer',
				'Illuminate\Contracts\Mail\Mailer',
				'Illuminate\Contracts\Mail\MailQueue',
			],
			//'member'                    => [\LaravelBoot\Foundation\Member\MemberManagement::class],
			'module'                    => ['LaravelBoot\Foundation\Module\ModuleManager'],
			'permission'                => ['LaravelBoot\Foundation\Permission\PermissionManager'],
			'permission.group'          => ['LaravelBoot\Foundation\Permission\PermissionGroupManager'],
			'permission.module'         => ['LaravelBoot\Foundation\Permission\PermissionModuleManager'],
			'permission.type'           => ['LaravelBoot\Foundation\Permission\PermissionTypeManager'],
			'queue'                     => [
				'Illuminate\Queue\QueueManager',
				'Illuminate\Contracts\Queue\Factory',
				'Illuminate\Contracts\Queue\Monitor',
			],
			'queue.connection'          => ['Illuminate\Contracts\Queue\Queue'],
			'queue.failer'              => ['Illuminate\Queue\Failed\FailedJobProviderInterface'],
			'redirect'                  => [
				'Illuminate\Routing\Redirector',
				'LaravelBoot\Foundation\Routing\Redirector',
			],
			'redis'                     => [
				'Illuminate\Redis\RedisManager',
				'Illuminate\Contracts\Redis\Factory',
			],
			'request'                   => [
				'Illuminate\Http\Request',
				'Symfony\Component\HttpFoundation\Request',
			],
			'router'                    => [
				'Illuminate\Routing\Router',
				'Illuminate\Contracts\Routing\Registrar',
			],
			//'searchengine.optimization' => [\LaravelBoot\Foundation\SearchEngine\Optimization::class],
			'session'                   => ['Illuminate\Session\SessionManager'],
			'session.store'             => [
				'Illuminate\Session\Store',
				'Symfony\Component\HttpFoundation\Session\SessionInterface',
			],
			'setting'                   => ['LaravelBoot\Foundation\Setting\Contracts\SettingsRepository'],
			'theme'                     => ['LaravelBoot\Foundation\Theme\ThemeManager'],
			'translator'                => [
				'Illuminate\Translation\Translator',
				'Illuminate\Contracts\Translation\Translator',
				'LaravelBoot\Foundation\Translation\Translator',
			],
			'url'                       => [
				'Illuminate\Routing\UrlGenerator',
				'Illuminate\Contracts\Routing\UrlGenerator',
			],
			'validator'                 => [
				'Illuminate\Validation\Factory',
				'Illuminate\Contracts\Validation\Factory',
			],
			'view'                      => [
				'Illuminate\View\Factory',
				'Illuminate\Contracts\View\Factory',
			],
			'yaml'                      => [
				'Symfony\Component\Yaml\Yaml',
			],
		];
		foreach ($aliases as $key => $aliases) {
			foreach ($aliases as $alias) {
				$this->alias($key, $alias);
			}
		}
	}

	/**
	 * Flush the container of all bindings and resolved instances.
	 */
	public function flush()
	{
		parent::flush();
		$this->loadedProviders = [];
	}

	/**
	 * Get the application namespace.
	 *
	 * @return string
	 * @throws \Illuminate\Contracts\Container\BindingResolutionException
	 */
	public function getNamespace()
	{
		if (!is_null($this->namespace)) {
			return $this->namespace;
		}
		$composer = json_decode(file_get_contents(base_path('composer.json')), true);
		foreach ((array)data_get($composer, 'autoload.psr-4') as $namespace => $path) {
			foreach ((array)$path as $pathChoice) {
				if (realpath(app_path()) == realpath(base_path() . '/' . $pathChoice)) {
					return $this->namespace = $namespace;
				}
			}
		}
		throw new RuntimeException('Unable to detect application namespace.');
	}

	/**
	 * Get application installation status.
	 *
	 * @return bool
	 */
	public function isInstalled()
	{
		if ($this->bound('installed')) {
			return true;
		} else {
			if (!file_exists($this->storagePath() . DIRECTORY_SEPARATOR . 'installed')) {
				return false;
			}
			$this->instance('installed', true);

			return true;
		}
	}
}