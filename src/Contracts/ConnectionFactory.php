<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/18 13:56
 * @version
 */
namespace LaravelBoot\Foundation\Contracts;

interface ConnectionFactory
{
    /**
     * ConnectionFactory constructor.
     * @param array $config
     * @TODO 负载均衡
     */
    public function __construct(array $config);

    public function create();

    public function close();

}