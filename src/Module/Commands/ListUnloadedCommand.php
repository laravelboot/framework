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
use LaravelBoot\Foundation\Module\ModuleManager;

/**
 * Class ListUnloadedCommand.
 */
class ListUnloadedCommand extends Command
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
        $this->setDescription('Show unloaded module list.');
        $this->setName('module:unloaded');
    }

    /**
     * @param \LaravelBoot\Foundation\Module\ModuleManager $manager
     *
     * @return bool
     */
    public function fire(ModuleManager $manager)
    {
        $modules = $manager->getUnloadedModules();
        $list = new Collection();
        $this->info('Modules list:');
        $modules->each(function (array $module) use ($list) {
            $data = collect($module['authors']);
            $author = $data->get('name');
            $data->has('email') ? $author .= ' <' . $data->get('email') . '>' : null;
            $list->push([
                $module['identification'],
                $author,
                $module['description'],
                $module['directory'],
                $module['provider'],
                'Normal'
            ]);
        });
        $this->table($this->headers, $list->toArray());

        return true;
    }
}
