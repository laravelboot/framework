<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/18 14:03
 * @version
 */
namespace LaravelBoot\Foundation\Contracts;

class FilterChain
{
    private $request = null;
    private $response = null;
    private $context = null;

    private $filters = [];

    public function __construct($request, $response, $context)
    {
        $this->request = $request;
        $this->response = $response;
        $this->context = $context;
        $this->filters = [];
    }

    public function addFilter(Filter $filter)
    {
        $this->filters[] = $filter;
    }

    public function execute()
    {
        if(empty($this->filters)){
            return null;
        }

        foreach($this->filters as $filter){
            $filter->doFilter(
                $this->request,
                $this->response,
                $this->context
            );
        }
    }
}