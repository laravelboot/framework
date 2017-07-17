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
namespace LaravelBoot\Foundation\Translation;

use Illuminate\Translation\TranslationServiceProvider as IlluminateTranslationServiceProvider;

/**
 * Class TranslationServiceProvider.
 */
class TranslationServiceProvider extends IlluminateTranslationServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerLoader();

        $this->app->singleton('translator', function ($app) {
            $loader = $app['translation.loader'];
            $locale = $app['config']['app.locale'];
            $trans = new Translator($loader, $locale, $app['files']);
            $trans->setFallback($app['config']['app.fallback_locale']);

            return $trans;
        });
    }
}
