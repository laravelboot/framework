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

use Closure;
use Illuminate\Console\Command;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class RouteListCommand.
 */
class RouteListCommand extends Command
{
    /**
     * @var string
     */
    protected $name = 'route:list';

    /**
     * @var string
     */
    protected $description = 'List all registered routes';

    /**
     * @var \Illuminate\Routing\Router
     */
    protected $router;

    /**
     * @var \Illuminate\Routing\RouteCollection
     */
    protected $routes;

    /**
     * @var array
     */
    protected $headers = [
        'Domain',
        'Method',
        'URI',
        'Name',
        'Action',
        'Middleware',
    ];

    /**
     * RouteListCommand constructor.
     *
     * @param \Illuminate\Routing\Router $router
     */
    public function __construct(Router $router)
    {
        parent::__construct();
        $this->router = $router;
        $this->routes = $router->getRoutes();
    }

    /**
     * Command handler.
     *
     * @return bool
     */
    public function fire()
    {
        if (count($this->routes) == 0) {
            $this->error("Your application doesn't have any routes.");

            return false;
        }
        $this->displayRoutes($this->getRoutes());

        return true;
    }

    /**
     * Compile the routes into a displayable format.
     *
     * @return array
     */
    protected function getRoutes()
    {
        $results = [];
        foreach ($this->routes as $route) {
            $results[] = $this->getRouteInformation($route);
        }
        if ($sort = $this->option('sort')) {
            $results = Arr::sort($results, function ($value) use ($sort) {
                return $value[$sort];
            });
        }
        if ($this->option('reverse')) {
            $results = array_reverse($results);
        }

        return array_filter($results);
    }

    /**
     * Compile the routes into a displayable format.
     *
     * @param \Illuminate\Routing\Route $route
     *
     * @return array|null
     */
    protected function getRouteInformation(Route $route)
    {
        return $this->filterRoute([
            'host'       => $route->domain(),
            'method'     => implode('|', $route->methods()),
            'uri'        => $route->uri(),
            'name'       => $route->getName(),
            'action'     => $route->getActionName(),
            'middleware' => $this->getMiddleware($route),
        ]);
    }

    /**
     * Display the route information on the console.
     *
     * @param array $routes
     */
    protected function displayRoutes(array $routes)
    {
        $this->table($this->headers, $routes);
    }

    /**
     * Get middleware of a route.
     *
     * @param $route
     *
     * @return string
     */
    protected function getMiddleware($route)
    {
        return collect($route->gatherMiddleware())->map(function ($middleware) {
            return $middleware instanceof Closure ? 'Closure' : $middleware;
        })->implode(',');
    }

    /**
     * Filter the route by URI and / or name.
     *
     * @param array $route
     *
     * @return array|void
     */
    protected function filterRoute(array $route)
    {
        if (($this->option('name') && !Str::contains($route['name'],
                    $this->option('name'))) || $this->option('path') && !Str::contains($route['uri'],
                $this->option('path')) || $this->option('method') && !Str::contains($route['method'],
                $this->option('method'))
        ) {
            return;
        }

        return $route;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            [
                'method',
                null,
                InputOption::VALUE_OPTIONAL,
                'Filter the routes by method.',
            ],
            [
                'name',
                null,
                InputOption::VALUE_OPTIONAL,
                'Filter the routes by name.',
            ],
            [
                'path',
                null,
                InputOption::VALUE_OPTIONAL,
                'Filter the routes by path.',
            ],
            [
                'reverse',
                'r',
                InputOption::VALUE_NONE,
                'Reverse the ordering of the routes.',
            ],
            [
                'sort',
                null,
                InputOption::VALUE_OPTIONAL,
                'The column (host, method, uri, name, action, middleware) to sort by.',
                'uri',
            ],
        ];
    }
}
