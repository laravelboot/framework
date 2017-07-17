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
namespace LaravelBoot\Foundation\Permission\Commands;

use Illuminate\Support\Collection;
use LaravelBoot\Foundation\Console\Abstracts\Command;
use LaravelBoot\Foundation\Permission\Permission;

/**
 * Class PermissionCommand.
 */
class PermissionCommand extends Command
{
    /**
     * @var array
     */
    protected $headers = [
        'Identification',
        'Description',
    ];

    /**
     * Configure Command.
     */
    public function configure()
    {
        $this->setDescription('Show permission list.');
        $this->setName('permission:list');
    }

    /**
     * Command Handler.
     *
     * @return bool
     */
    public function fire()
    {
        $data = new Collection();
        $this->container->make('permission')->permissions()->each(function (Permission $permission, $identification) use ($data) {
            $data->push([
                $identification,
                $permission->description(),
            ]);
        });
        $this->table($this->headers, $data->toArray());

        return true;
    }
}
