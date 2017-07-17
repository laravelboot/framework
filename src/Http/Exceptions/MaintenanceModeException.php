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
namespace LaravelBoot\Foundation\Http\Exceptions;

use Carbon\Carbon;
use Exception;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

/**
 * Class MaintenanceModeException.
 */
class MaintenanceModeException extends ServiceUnavailableHttpException
{
    /**
     * @var int
     */
    public $wentDownAt;

    /**
     * @var \Carbon\Carbon
     */
    public $retryAfter;

    /**
     * @var \Carbon\Carbon
     */
    public $willBeAvailableAt;

    /**
     * MaintenanceModeException constructor.
     *
     * @param int        $time
     * @param int        $retryAfter
     * @param string     $message
     * @param \Exception $previous
     * @param int        $code
     */
    public function __construct($time, $retryAfter = null, $message = null, Exception $previous = null, $code = 0)
    {
        parent::__construct($retryAfter, $message, $previous, $code);
        $this->wentDownAt = Carbon::createFromTimestamp($time);
        if ($retryAfter) {
            $this->retryAfter = $retryAfter;
            $this->willBeAvailableAt = $this->wentDownAt->addSeconds($this->retryAfter);
        }
    }
}
