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
namespace LaravelBoot\Foundation\Console;

use Illuminate\Console\Application as IlluminateApplication;
use Illuminate\Container\Container;
use Illuminate\Contracts\Console\Application as ApplicationContract;
use Illuminate\Contracts\Events\Dispatcher;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class Application.
 */
class Application extends IlluminateApplication implements ApplicationContract
{
    /**
     * @var \Illuminate\Contracts\Container\Container|\Illuminate\Contracts\Foundation\Application|\LaravelBoot\Foundation\Application
     */
    protected $container;

    /**
     * @var \Symfony\Component\Console\Output\BufferedOutput
     */
    protected $lastOutput;

    /**
     * Application constructor.
     *
     * @param \Illuminate\Container\Container         $container
     * @param \Illuminate\Contracts\Events\Dispatcher $events
     * @param                                         $version
     */
    public function __construct(Container $container, Dispatcher $events, $version)
    {
        parent::__construct($container, $events, $version);
        $this->container = $container;
    }

    /**
     * Run an Artisan console command by name.
     *
     * @param string $command
     * @param array  $parameters
     * @param null   $outputBuffer
     *
     * @return int
     * @throws \Exception
     * @throws \Throwable
     */
    public function call($command, array $parameters = [], $outputBuffer = null)
    {
        $parameters = collect($parameters)->prepend($command);
        $this->lastOutput = $outputBuffer ?: new BufferedOutput();
        $this->setCatchExceptions(false);
        $result = $this->run(new ArrayInput($parameters->toArray()), $this->lastOutput);
        $this->setCatchExceptions(true);

        return $result;
    }

    /**
     * Add a command, resolving through the application.
     *
     * @param string $command
     *
     * @return \Symfony\Component\Console\Command\Command
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function resolve($command)
    {
        if (is_null($this->container)) {
            $this->container = Container::getInstance();
        }

        return $this->add($this->container->make($command));
    }

    /**
     * Resolve an array of commands through the application.
     *
     * @param array|mixed $commands
     *
     * @return $this
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function resolveCommands($commands)
    {
        $commands = is_array($commands) ? $commands : func_get_args();
        foreach ($commands as $command) {
            $this->resolve($command);
        }

        return $this;
    }

    /**
     * Get IoC Container.
     *
     * @return \Illuminate\Contracts\Foundation\Application
     */
    public function getContainer()
    {
        return $this->container;
    }
}
