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
namespace LaravelBoot\Foundation\Network\Http\Request;

use LaravelBoot\Foundation\Network\Http\Bag\HeaderBag;
use LaravelBoot\Foundation\Network\Http\Bag\ParameterBag;
use LaravelBoot\Foundation\Network\Http\Bag\ServerBag;
use LaravelBoot\Foundation\Network\Http\Request\AcceptHeader\AcceptHeader;
use LaravelBoot\Foundation\Utility\Types\Ip;
use LaravelBoot\Foundation\Network\Http\Bag\FileBag;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * Request represents an HTTP request.
 *
 * The methods dealing with URL accept / return a raw path (% encoded):
 *   * getBasePath
 *   * getBaseUrl
 *   * getPathInfo
 *   * getRequestUri
 *   * getUri
 *   * getUriForPath
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class BaseRequest extends SymfonyRequest
{
}
