<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/24 19:21
 * @version
 */
namespace LaravelBoot\Foundation\Module\Commands;

use Illuminate\Console\Command;
use LaravelBoot\Foundation\Application;
use LaravelBoot\Foundation\Module\Module;
use LaravelBoot\Foundation\Module\ModuleManager;
use LaravelBoot\Foundation\Module\Abstracts\Installer;

/**
 * Class ListCommand.
 */
class OpCommand extends Command
{
    protected $name = 'module:op';
    protected $signature = 'module:op {--install=} {--update=} {--module_name=}';
    protected $description = 'module op:install/update';

    /**
     * Command Handler.
     *
     *
     * @return bool
     */
    public function fire()
    {
        $manager = Application::getInstance()->make('module');
        $install = $this->option('install');
        //$update  = $this->option('update');
        $name = $this->option('module_name');
        if($install){
            $module = $manager->get($name);
            if ($module && method_exists($provider = $module->getEntry(), 'install')) {
                if (($installer = Application::getInstance()->make(call_user_func([$provider, 'install']))) instanceof Installer) {
                    $installer->setModule($module);
                    if ($installer->install()) {
                        $this->info('install success');
                    } else {
                        $this->error('install fail');
                    }
                }
            }
            return $this->error('module['.$name.'] is not exists');
        }
        return $this->error('param is not empty');

    }
}