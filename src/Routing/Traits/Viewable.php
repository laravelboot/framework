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
namespace LaravelBoot\Foundation\Routing\Traits;

use Illuminate\Support\Str;

/**
 * Trait Viewable.
 */
trait Viewable
{
    /**
     * Get view instance.
     *
     * @return \Illuminate\Contracts\View\Factory
     */
    protected function getView()
    {
        return $this->container->make('view');
    }

    /**
     * Share variable with view.
     *
     * @param      $key
     * @param null $value
     */
    protected function share($key, $value = null)
    {
        $this->getView()->share($key, $value);
    }

    /**
     * Share variable with view.
     *
     * @param       $template
     * @param array $data
     * @param array $mergeData
     *
     * @return \Illuminate\Contracts\View\View
     */
    protected function view($template, array $data = [], $mergeData = [])
    {
        if (Str::contains($template, '::')) {
            return $this->getView()->make($template, $data, $mergeData);
        } else {
            return $this->getView()->make('theme::' . $template, $data, $mergeData);
        }
    }
}
