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
use RuntimeException;

/**
 * Class ViewClearCommand.
 */
class ViewClearCommand extends Command
{
    /**
     * @var string
     */
    protected $name = 'view:clear';

    /**
     * @var string
     */
    protected $description = 'Clear all compiled view files';

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * ViewClearCommand constructor.
     *
     * @param \Illuminate\Filesystem\Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * Command handler.
     */
    public function fire()
    {
        $path = $this->laravel['config']['view.compiled'];
        if (!$path) {
            throw new RuntimeException('View path not found.');
        }
        foreach ($this->files->glob("{$path}/*") as $view) {
            $this->files->delete($view);
        }
        $this->info('Compiled views cleared!');
    }
}
