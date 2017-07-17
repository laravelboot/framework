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
namespace LaravelBoot\Foundation\Translation\Events;

/**
 * Class LocaleUpdated.
 */
class LocaleUpdated
{
    /**
     * The new locale.
     *
     * @var string
     */
    public $locale;

    /**
     * Create a new event instance.
     *
     * @param string $locale
     */
    public function __construct($locale)
    {
        $this->locale = $locale;
    }
}
