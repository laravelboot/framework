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
namespace LaravelBoot\Foundation\Passport\Abstracts;

use Exception;
use LaravelBoot\Foundation\Passport\Responses\ApiResponse;

/**
 * Class SetHandler.
 */
abstract class SetHandler extends DataHandler
{
    /**
     * Make execute result to response with errors or messages.
     *
     * @return \LaravelBoot\Foundation\Passport\Responses\ApiResponse
     * @throws \Exception
     */
    public function toResponse()
    {
        $response = new ApiResponse();
        try {
            $result = $this->execute();
            if ($result) {
                $messages = $this->messages();
            } else {
                $messages = $this->errors();
            }

            return $response->withParams([
                'code' => $this->code(),
                'data' => $this->data(),
                'message' => $messages,
            ]);
        } catch (Exception $exception) {
            return $this->handleExceptions($response, $exception);
        }
    }
}
