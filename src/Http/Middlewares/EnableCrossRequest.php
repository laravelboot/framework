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
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EnableCrossRequest.
 */
class EnableCrossRequest
{
    /**
     * @var \Illuminate\Contracts\Routing\ResponseFactory
     */
    protected $response;

    /**
     * EnableCrossRequest constructor.
     *
     * @param \Illuminate\Contracts\Routing\ResponseFactory $response
     */
    public function __construct(ResponseFactory $response)
    {
        $this->response = $response;
    }

    /**
     * Middleware handler.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $headers = collect([
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Headers'     => 'Origin,Content-Type,Cookie,Accept,Authorization,X-Requested-With',
            'Access-Control-Allow-Methods'     => 'GET,POST,PATCH,PUT,OPTIONS',
            'Access-Control-Allow-Credentials' => 'true',
        ]);
        $response = $next($request);
        if ($response instanceof Response) {
            $headers->each(function ($value, $key) use($response) {
                $response->headers->set($key, $value);
            });
        } else {
            $headers->each(function ($value, $key) use($response) {
                $response->header($key, $value);
            });
        }

        return $response;
    }
}