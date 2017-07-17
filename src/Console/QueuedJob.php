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

use Illuminate\Contracts\Console\Kernel as KernelContract;

/**
 * Class QueuedJob.
 */
class QueuedJob
{
    /**
     * @var \Illuminate\Contracts\Console\Kernel
     */
    protected $kernel;

    /**
     * QueuedJob constructor.
     *
     * @param \Illuminate\Contracts\Console\Kernel $kernel
     */
    public function __construct(KernelContract $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * Command handler.
     *
     * @param \Illuminate\Queue\Jobs\Job $job
     * @param array                      $data
     *
     * @return void
     */
    public function fire($job, $data)
    {
        call_user_func_array([
            $this->kernel,
            'call',
        ], $data);
        $job->delete();
    }
}
