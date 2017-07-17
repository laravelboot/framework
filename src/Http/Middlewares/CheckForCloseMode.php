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
namespace LaravelBoot\Foundation\Http\Middlewares;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Routing\Router;
use Illuminate\Support\Str;
use LaravelBoot\Foundation\Setting\Contracts\SettingsRepository;

/**
 * Class CheckForCloseMode.
 */
class CheckForCloseMode
{
    /**
     * @var \Illuminate\Contracts\Foundation\Application|\LaravelBoot\Foundation\Application
     */
    protected $application;

    /**
     * @var \Illuminate\Routing\Router
     */
    protected $router;

    /**
     * @var \Illuminate\Contracts\Routing\ResponseFactory
     */
    protected $response;

    /**
     * CheckForMaintenanceMode constructor.
     *
     * @param \Illuminate\Contracts\Foundation\Application|\LaravelBoot\Foundation\Application $application
     * @param \Illuminate\Contracts\Routing\ResponseFactory                               $response
     * @param \Illuminate\Routing\Router                                                  $router
     */
    public function __construct(Application $application, ResponseFactory $response, Router $router)
    {
        $this->application = $application;
        $this->response = $response;
        $this->router = $router;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->application->isInstalled()) {
            if (!$this->application->make(SettingsRepository::class)->get('site.enabled', true) && !Str::is('admin*', $this->router->current()->uri()) && !Str::is('api*', $this->router->current()->uri())) {
                return $this->response->make('网站已经关闭！');
            }
        }

        return $next($request);
    }
}
