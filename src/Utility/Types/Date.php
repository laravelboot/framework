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
namespace LaravelBoot\Foundation\Utility\Types;

class Date
{
    private $timestamp = null;

    public function __construct($timestamp = null)
    {
        if (null !== $timestamp && is_int($timestamp)) {
            $this->timestamp = $timestamp;
            return true;
        }

        $this->timestamp = time();
    }

    public function isToday()
    {
        return date('Ymd', $this->timestamp) == date('Ymd', strtotime('today'));
    }

    public function isYesterday()
    {
        return date('Ymd', $this->timestamp) == date('Ymd', strtotime('yesterday'));
    }

    public function isTomorrow()
    {
        return date('Ymd', $this->timestamp) == date('Ymd', strtotime('tomorrow'));
    }

}