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
namespace LaravelBoot\Foundation\Console\Abstracts;

use Exception;
use Illuminate\Container\Container;
use Illuminate\Contracts\Support\Arrayable;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Class Command.
 */
abstract class Command extends SymfonyCommand
{
    /**
     * @var \Illuminate\Container\Container|\LaravelBoot\Foundation\Application
     */
    protected $container;

    /**
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    protected $input;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * Command constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->container = $this->getContainer();
    }

    /**
     * Prompt the user for input.
     *
     * @param      $question
     * @param null $default
     *
     * @return string
     */
    protected function ask($question, $default = null)
    {
        $question = new Question("<question>$question</question> ", $default);

        return $this->getHelperSet()->get('question')->ask($this->input, $this->output, $question);
    }

    /**
     * Call another console command.
     *
     * @param       $command
     * @param array $arguments
     *
     * @return int
     * @throws \Exception
     */
    public function call($command, array $arguments = [])
    {
        $instance = $this->getApplication()->find($command);
        $arguments['command'] = $command;

        return $instance->run(new ArrayInput($arguments), $this->output);
    }

    /**
     * Write a string as error output.
     *
     * @param $string
     */
    public function error($string)
    {
        $this->output->writeln("<error>$string</error>");
    }

    /**
     * Execute the console command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @throws \Exception
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        if (!method_exists($this, 'fire')) {
            throw new Exception('Method fire do not exits!', 404);
        }

        return $this->container->call([$this, 'fire']);
    }

    /**
     * Get IoC Container.
     *
     * @return \Illuminate\Container\Container|\LaravelBoot\Foundation\Application
     */
    protected function getContainer()
    {
        return Container::getInstance();
    }

    /**
     * Get command's input instance.
     *
     * @return \Symfony\Component\Console\Input\InputInterface
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * Get command's output instance.
     *
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Determine if the given option is present.
     *
     * @param $name
     *
     * @return bool
     */
    protected function hasOption($name)
    {
        return $this->input->hasOption($name);
    }

    /**
     * Write a string as information output.
     *
     * @param $string
     */
    protected function info($string)
    {
        $this->output->writeln("<info>$string</info>");
    }

    /**
     * Prompt the user for input but hide the answer from the console.
     *
     * @param $question
     *
     * @return string
     */
    protected function secret($question)
    {
        $question = new Question("<question>$question</question> ");
        $question->setHidden(true)->setHiddenFallback(true);

        return $this->getHelperSet()->get('question')->ask($this->input, $this->output, $question);
    }

    /**
     * Set container's instance.
     *
     * @param $container
     */
    protected function setContainer($container)
    {
        $this->container = $container;
    }

    /**
     * Format input to textual table.
     *
     * @param array  $headers
     * @param        $rows
     * @param string $style
     */
    public function table(array $headers, $rows, $style = 'default')
    {
        $table = new Table($this->output);
        if ($rows instanceof Arrayable) {
            $rows = $rows->toArray();
        }
        $table->setHeaders($headers)->setRows($rows)->setStyle($style)->render();
    }
}
