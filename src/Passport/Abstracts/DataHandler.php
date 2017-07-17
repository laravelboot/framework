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
use Illuminate\Support\Collection;
use LaravelBoot\Foundation\Passport\Responses\ApiResponse;
use LaravelBoot\Foundation\Routing\Abstracts\Handler;

/**
 * Class DataHandler.
 */
abstract class DataHandler extends Handler
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var bool
     */
    protected $hasFilter = false;

    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Data for handler.
     *
     * @return array
     */
    public function data()
    {
        return $this->data instanceof Collection ? $this->data->toArray() : $this->data;
    }

    /**
     * Add filter to filters.
     *
     * @return $this
     */
    public function filter()
    {
        list($column, $operator, $value, $boolean) = func_get_args();
        empty($operator) && $operator = null;
        empty($value) && $value = null;
        empty($boolean) && $boolean = null;
        if($this->hasFilter) {
            $this->model = $this->model->where($column, $operator, $value, $boolean);
        } else {
            $this->model = $this->model->newQuery()->where($column, $operator, $value, $boolean);
            $this->hasFilter = true;
        }

        return $this;
    }

    /**
     * Make data to response with errors or messages.
     *
     * @return \LaravelBoot\Foundation\Passport\Responses\ApiResponse
     * @throws \Exception
     */
    public function toResponse()
    {
        $response = new ApiResponse();
        try {
            $data = $this->data();
            if (empty($data)) {
                $messages = $this->errors();
            } else {
                $messages = $this->messages();
            }

            return $response->withParams([
                'code'    => $this->code(),
                'data'    => $data,
                'message' => $messages,
            ]);
        } catch (Exception $exception) {
            return $this->handleExceptions($response, $exception);
        }
    }
}
