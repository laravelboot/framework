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
 * Class ClearCompiledCommand.
 */
class ClearCompiledCommand extends Command
{
    /**
     * @var string
     */
    protected $name = 'clear-compiled';

    /**
     * @var string
     */
    protected $description = 'Remove the compiled class file';

    /**
     * Command handler.
     */
    public function fire()
    {
        $compiledPath = $this->laravel->getCachedCompilePath();
        $servicesPath = $this->laravel->getCachedServicesPath();
        if (file_exists($compiledPath)) {
            @unlink($compiledPath);
        }
        if (file_exists($servicesPath)) {
            @unlink($servicesPath);
        }
        $this->info('The compiled class file has been removed.');
    }
}
