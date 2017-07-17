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
namespace LaravelBoot\Foundation\Module\Abstracts;

use Illuminate\Container\Container;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Collection;
use LaravelBoot\Foundation\Module\Module as BaseModule;
use LaravelBoot\Foundation\Setting\Contracts\SettingsRepository;
use LaravelBoot\Foundation\Translation\Translator;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class Installer.
 */
abstract class Installer
{
    /**
     * @var \Illuminate\Container\Container|\LaravelBoot\Foundation\Application
     */
    protected $container;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $info;

    /**
     * @var \LaravelBoot\Foundation\Module\Module
     */
    protected $module;

    /**
     * @var \LaravelBoot\Foundation\Setting\Contracts\SettingsRepository
     */
    protected $settings;

    /**
     * @var \LaravelBoot\Foundation\Translation\Translator
     */
    protected $translator;

    /**
     * Installer constructor.
     *
     * @param \Illuminate\Container\Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->info = new Collection();
        $this->settings = $this->container->make(SettingsRepository::class);
        $this->translator = $this->container->make(Translator::class);
    }

    /**
     * Get console instance.
     *
     * @return \Illuminate\Contracts\Console\Kernel|\LaravelBoot\Foundation\Console\Application
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getConsole()
    {
        $kernel = $this->container->make(Kernel::class);
        $kernel->bootstrap();

        return $kernel->getArtisan();
    }

    /**
     * @return bool
     */
    abstract public function handle();

    /**
     * Return output info for installation.
     *
     * @return \Illuminate\Support\Collection
     */
    public function info()
    {
        return $this->info;
    }

    /**
     * @return bool
     * @throws \Exception
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public final function install()
    {
        if ($this->settings->get('module.' . $this->module->getIdentification() . '.installed', false)) {
            $this->info->put('errors', '模块标识[]已经被占用，如需继续安装，请卸载同标识插件！');

            return false;
        }

        $requires = collect($this->require());
        $noInstalled = new Collection();
        $requires->each(function ($require) use ($noInstalled) {
            if (!$this->settings->get('module.' . $require . '.installed', false)) {
                $noInstalled->push($require);
            }
        });

        if ($noInstalled->isNotEmpty()) {
            $this->info->put('errors', '依赖的模块[' . $noInstalled->implode(',') . ']尚未安装！');

            return false;
        }

        $provider = $this->module->getEntry();
        $this->container->getProvider($provider) || $this->container->register($provider);

        if ($this->handle()) {
            $input = new ArrayInput([
                '--force' => true,
            ]);
            $output = new BufferedOutput();
            $this->getConsole()->find('migrate')->run($input, $output);
            $this->getConsole()->find('vendor:publish')->run($input, $output);
            $log = explode(PHP_EOL, $output->fetch());
            $this->container->make('log')->info('install module:' . $this->module->getIdentification(), $log);
            $this->info->put('data', $log);
            $this->info->put('messages', '安装模块[' . $this->module->getIdentification() . ']成功！');
            $this->settings->set('module.' . $this->module->getIdentification() . '.installed', true);

            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    abstract public function _require ();

    /**
     * @param \LaravelBoot\Foundation\Module\Module $module
     */
    public function setModule(BaseModule $module)
    {
        $this->module = $module;
    }
}
