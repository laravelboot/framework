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
namespace LaravelBoot\Foundation\Passport\Responses;

use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;

/**
 * Class ApiResponse.
 */
class ApiResponse
{
    /**
     * @var array
     */
    protected $params = [];

    /**
     * Generate a api response to http response.
     *
     * @param \Psr\Http\Message\ResponseInterface|null $response
     * @param array                                    $params
     *
     * @return \LaravelBoot\Foundation\Passport\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     */
    public function generateHttpResponse(ResponseInterface $response = null, $params = [])
    {
        is_null($response) && $response = new Response();
        $params && $this->params = array_merge($this->params, $params);
        $status = collect($this->params)->get('code', 200);
        if (!is_int($status)) {
            $status = 500;
        }
        if ($status > 598 || $status < 100) {
            $status = 500;
        }
        $response = $response->withStatus($status)
            ->withHeader('pragma', 'no-cache')
            ->withHeader('cache-control', 'no-store')
            ->withHeader('content-type', 'application/json; charset=UTF-8');
        $response->getBody()->write(json_encode($this->params,JSON_UNESCAPED_UNICODE));

        return $response;
    }

    /**
     * Add params to api response.
     *
     * @param array $params
     *
     * @return $this
     */
    public function withParams($params = [])
    {
        $this->params = array_merge($this->params, $params);

        return $this;
    }
}
