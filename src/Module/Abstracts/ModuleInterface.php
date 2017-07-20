<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/20 10:45
 * @version
 */
namespace LaravelBoot\Foundation\Module\Abstracts;

interface ModuleInterface
{
    /**
     * Name of module.
     *
     * @return string
     */
    public static function name();

    /**
     * Description of module
     *
     * @return string
     */
    public static function description();

    /**
     * Install for module.
     *
     * @return string
     */
    public static function install();

    /**
     * Register module extra providers.
     */
    public function register();

    /**
     * Uninstall for module.
     *
     * @return string
     */
    public static function uninstall();

    /**
     * Version of module.
     *
     * @return string
     */
    public static function version();
}