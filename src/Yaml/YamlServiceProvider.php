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
namespace LaravelBoot\Foundation\Yaml;

use LaravelBoot\Foundation\Http\Abstracts\ServiceProvider;
use Symfony\Component\Yaml\Yaml;

/**
 * Class YamlServiceProvider.
 */
class YamlServiceProvider extends ServiceProvider
{
    /**
     * Register instance.
     */
    public function register() {
        $this->app->singleton('yaml', function () {
            return new Yaml();
        });
    }
}
