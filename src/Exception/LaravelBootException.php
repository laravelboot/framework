<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/18 15:11
 * @version
 */
namespace LaravelBoot\Foundation\Exception;

class LaravelBootException extends \Exception
{
    /**
     * @var null
     *  * null : do not logging
     *  * LogLevel CONST ...
     */
    public $logLevel = null;

    /**
     * 用于记录异常出现时的上下文信息
     * @var array
     */
    private $metaData = [];

    public function __construct($message = '', $code = 0, Exception $previous = null, array $metaData = [])
    {
        parent::__construct($message, $code, $previous);

        $this->metaData = $metaData;
    }

    public function getMetadata()
    {
        return $this->metaData;
    }

    public function setMetadata(array $metaData)
    {
        $this->metaData = $metaData;
    }
}