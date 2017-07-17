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
namespace LaravelBoot\Foundation\Http\Listeners;

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Routing\Events\RouteMatched;
use LaravelBoot\Foundation\Event\Abstracts\EventSubscriber;

/**
 * Class CheckPublicPath.
 */
class CheckPublicPath extends EventSubscriber
{
    /**
     * @var \Illuminate\Routing\Router
     */
    protected $request;

    /**
     * CheckPublicPath constructor.
     *
     * @param \Illuminate\Container\Container $container
     * @param \Illuminate\Events\Dispatcher   $events
     * @param \Illuminate\Http\Request        $request
     */
    public function __construct(Container $container, Dispatcher $events, Request $request)
    {
        parent::__construct($container, $events);
        $this->request = $request;
    }

    /**
     * Name of event.
     *
     * @throws \Exception
     * @return string|object
     */
    protected function getEvent()
    {
        return RouteMatched::class;
    }

    public function handle()
    {
        if ($this->request->getBasePath() == '/public') {
            throw new \Exception('public目录 必须为网站根目录');
        }
    }
}
