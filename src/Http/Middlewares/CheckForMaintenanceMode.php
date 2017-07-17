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
use LaravelBoot\Foundation\Http\Exceptions\MaintenanceModeException;

/**
 * Class CheckForMaintenanceMode.
 */
class CheckForMaintenanceMode
{
    /**
     * @var \Illuminate\Contracts\Foundation\Application|\LaravelBoot\Foundation\Application
     */
    protected $application;

    /**
     * CheckForMaintenanceMode constructor.
     *
     * @param \Illuminate\Contracts\Foundation\Application|\LaravelBoot\Foundation\Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
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
        if ($this->application->isDownForMaintenance()) {
            $data = json_decode(file_get_contents($this->application->storagePath() . '/bootstraps/down'), true);
            throw new MaintenanceModeException($data['time'], $data['retry'], $data['message']);
        }

        return $next($request);
    }
}
