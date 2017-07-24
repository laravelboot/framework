<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/19 13:54
 * @version
 */
namespace LaravelBoot\Foundation\Network\Monitor;


interface Hawker
{
    /**
     * 上报数据
     */
    public function report();

    /**
     * 上报使用内存等信息
     * @param $biz
     * @param $metrics
     * @param $tags
     */
    public function add($biz, array $metrics, array $tags = []);

    /**
     * 上报请求成功时长
     * @param $side  Hawk::SERVER or Hawk::CLIENT
     * @param $service  服务名
     * @param $method   方法名
     * @param $ip       对端ip
     * @param $diffSec  请求处理时长
     */
    public function addTotalSuccessTime($side, $service, $method, $ip, $diffSec);

    /**
     * 上报请求失败时长
     * @param $side  Hawk::SERVER or Hawk::CLIENT
     * @param $service  服务名
     * @param $method   方法名
     * @param $ip       对端ip
     * @param $diffSec  请求处理时长
     */
    public function addTotalFailureTime($side, $service, $method, $ip, $diffSec);

    /**
     * 增加请求成功次数
     * @param $side  Hawk::SERVER or Hawk::CLIENT
     * @param $service  服务名
     * @param $method   方法名
     * @param $ip       对端ip
     */
    public function addTotalSuccessCount($side, $service, $method, $ip);

    /**
     * 增加请求失败次数
     * @param $side  Hawk::SERVER or Hawk::CLIENT
     * @param $service  服务名
     * @param $method   方法名
     * @param $ip       对端ip
     */
    public function addTotalFailureCount($side, $service, $method, $ip);
}