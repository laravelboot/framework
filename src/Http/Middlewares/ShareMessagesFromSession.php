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

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Http\Request;
use Illuminate\Support\ViewErrorBag;

/**
 * Class ShareMessagesFromSession.
 */
class ShareMessagesFromSession
{
    /**
     * @var \Illuminate\Contracts\View\Factory
     */
    protected $view;

    /**
     * ShareMessagesFromSession constructor.
     *
     * @param \Illuminate\Contracts\View\Factory $view
     */
    public function __construct(ViewFactory $view)
    {
        $this->view = $view;
    }

    /**
     * Middleware handler.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        $this->view->share(
            'errors', $request->session()->get('messages') ?: new ViewErrorBag
        );

        return $next($request);
    }
}
