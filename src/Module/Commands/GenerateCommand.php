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

use LaravelBoot\Foundation\Console\Abstracts\Command;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class GenerateCommand.
 */
class GenerateCommand extends Command
{
    /**
     * Configure command.
     */
    public function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The name of module.');
        $this->setDescription('To generate a module from template.');
        $this->setName('module:generate');
    }

    /**
     * Command handler.
     *
     * @return bool
     */
    public function fire(): bool
    {
        return true;
    }
}
