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
namespace LaravelBoot\Foundation\Flow;

use LaravelBoot\Foundation\Flow\Contracts\SupportStrategy;

/**
 * Class ClassInstanceSupportStrategy.
 */
class ClassInstanceSupportStrategy implements SupportStrategy
{
    /**
     * @var string
     */
    private $className;

    /**
     * @param string $className a FQCN
     */
    public function __construct($className)
    {
        $this->className = $className;
    }

    /**
     * @param Flow   $workflow
     * @param object $subject
     *
     * @return bool
     */
    public function supports(Flow $workflow, $subject)
    {
        return $subject instanceof $this->className;
    }
}
