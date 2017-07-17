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

/**
 * Class UpCommand.
 */
class UpCommand extends Command
{
    /**
     * @var string
     */
    protected $name = 'up';
    /**
     * @var string
     */
    protected $description = 'Bring the application out of maintenance mode';

    /**
     * Command handler.
     */
    public function fire()
    {
        @unlink($this->laravel->storagePath() . '/bootstraps/down');
        $this->info('Application is now live.');
    }
}
