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
namespace LaravelBoot\Foundation\Composer;

use Composer\Script\Event;
use LaravelBoot\Foundation\Application;

/**
 * Class ComposerScripts.
 */
class ComposerScripts
{
    /**
     * Post Install Handler for composer install.
     *
     * @param \Composer\Script\Event $event
     *
     * @return void
     */
    public static function postInstall(Event $event)
    {
        require_once $event->getComposer()->getConfig()->get('vendor-dir') . '/autoload.php';
        static::clearCompiled();
    }

    /**
     * Post Update Handler for composer update.
     *
     * @param \Composer\Script\Event $event
     *
     * @return void
     */
    public static function postUpdate(Event $event)
    {
        require_once $event->getComposer()->getConfig()->get('vendor-dir') . '/autoload.php';
        static::clearCompiled();
    }

    /**
     * Clear compiled files for LaravelBoot.
     */
    protected static function clearCompiled()
    {
        file_exists($servicesPath = (new Application(getcwd()))->getCachedCompilePath()) && @unlink($servicesPath);
    }
}
