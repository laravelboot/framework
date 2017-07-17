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
 * Class EnvironmentCommand.
 */
class EnvironmentCommand extends Command
{
    /**
     * @var string
     */
    protected $name = 'env';

    /**
     * @var string
     */
    protected $description = 'Display the current framework environment';

    /**
     * Command handler.
     */
    public function fire()
    {
        $this->line('<info>Current application environment:</info> <comment>' . $this->laravel['env'] . '</comment>');
    }
}
