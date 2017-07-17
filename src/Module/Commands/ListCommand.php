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
namespace LaravelBoot\Foundation\Module\Commands;

use Illuminate\Support\Collection;
use LaravelBoot\Foundation\Console\Abstracts\Command;
use LaravelBoot\Foundation\Module\Module;
use LaravelBoot\Foundation\Module\ModuleManager;

/**
 * Class ListCommand.
 */
class ListCommand extends Command
{
    /**
     * @var array
     */
    protected $headers = [
        'Module Name',
        'Author',
        'Description',
        'Module Path',
        'Entry',
        'Status',
    ];

    /**
     * Configure Command.
     */
    public function configure()
    {
        $this->setDescription('Show module list.');
        $this->setName('module:list');
    }

    /**
     * Command Handler.
     *
     * @param \LaravelBoot\Foundation\Module\ModuleManager $manager
     *
     * @return bool
     */
    public function fire(ModuleManager $manager): bool
    {
        $modules = $manager->getModules();
        $list = new Collection();
        $this->info('Modules list:');
        $modules->each(function (Module $module, $path) use ($list) {
            $list->push([
                $module->getIdentification(),
                collect($module->getAuthor())->first(),
                $module->getDescription(),
                $path,
                $module->getEntry(),
                'Normal'
            ]);
        });
        $this->table($this->headers, $list->toArray());

        return true;
    }
}