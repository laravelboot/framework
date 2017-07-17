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
namespace LaravelBoot\Foundation\Flow\Contracts;

use LaravelBoot\Foundation\Flow\Flow;

/**
 * Interface SupportStrategy.
 */
interface SupportStrategy
{
    /**
     * @param Flow   $workflow
     * @param object $subject
     *
     * @return bool
     */
    public function supports(Flow $workflow, $subject);
}
