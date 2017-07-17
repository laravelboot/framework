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

/**
 * Class ConfigClearCommand.
 */
class ConfigClearCommand extends Command
{
    /**
     * @var string
     */
    protected $name = 'config:clear';

    /**
     * @var string
     */
    protected $description = 'Remove the configuration cache file';

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * ConfigClearCommand constructor.
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
        $this->files->delete($this->laravel->getCachedConfigPath());
        $this->info('Configuration cache cleared!');
    }
}
