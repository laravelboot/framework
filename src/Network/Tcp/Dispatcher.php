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
namespace LaravelBoot\Foundation\Network\Tcp;


use LaravelBoot\Foundation\Application;
use LaravelBoot\Foundation\Contracts\Context;

class Dispatcher
{
    /**
     * @var Request
     */
    private $request = null;
    private $context = null;

    public function dispatch(Request $request, Context $context)
    {
        $this->request = $request;
        $this->context = $context;

        yield $this->runService();
    }

    private function runService()
    {
        $serviceName = $this->getServiceName();

        $service = new $serviceName();

        if ($this->request->isGenericInvoke()) {
            $method = $this->request->getGenericMethodName();
        } else {
            $method = $this->request->getMethodName();
        }

        $args    = $this->request->getArgs();
        $args    = is_array($args) ? $args : [$args];

        yield $service->$method(...array_values($args));
    }

    private function getServiceName()
    {
        $app = Application::getInstance();
        $appNamespace = $app->getNamespace();
        $appName = $app->getName();

        if ($this->request->isGenericInvoke()) {
            $serviceName = $this->request->getGenericServiceName();
        } else {
            $serviceName = $this->request->getNovaServiceName();
        }

        $serviceName = str_replace('.', '\\', $serviceName);
        //$serviceName = Nova::removeNovaNamespace($serviceName, $appName);
        $serviceName = $appNamespace . $serviceName;

        return $serviceName;
    }
}