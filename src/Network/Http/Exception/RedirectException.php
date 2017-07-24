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
namespace LaravelBoot\Foundation\Network\Http\Exception;

use LaravelBoot\Foundation\Exception\LaravelBootException;

class RedirectException extends LaravelBootException
{
    public $redirectUrl;

    public function __construct($url, $message)
    {
        parent::__construct($message);
        $this->setRedirectUrl($url);
    }

    /**
     * @return mixed
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * @param mixed $redirectUrl
     */
    public function setRedirectUrl($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;
    }


}

