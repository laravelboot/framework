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
namespace LaravelBoot\Foundation\Network\Contracts;

use LaravelBoot\Foundation\Network\Connection\NovaClientPool;

interface LoadBalancingStrategyInterface
{
    public function get();
    public function initServers(NovaClientPool $pool);
}