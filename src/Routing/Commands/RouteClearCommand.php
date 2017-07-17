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
namespace LaravelBoot\Foundation\Routing\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

/**
 * Class RouteClearCommand.
 */
class RouteClearCommand extends Command
{
    /**
     * @var string
     */
    protected $name = 'route:clear';

    /**
     * @var string
     */
    protected $description = 'Remove the route cache file';

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * RouteClearCommand constructor.
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
        $this->files->delete($this->laravel->getCachedRoutesPath());
        $this->info('Route cache cleared!');
    }
}
