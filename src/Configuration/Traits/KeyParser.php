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
namespace LaravelBoot\Foundation\Configuration\Traits;

/**
 * Class KeyParser.
 */
trait KeyParser
{
    /**
     * @var array
     */
    protected $keyParserCache = [];

    /**
     * Set the parsed value of a key.
     *
     * @param string $key
     * @param array  $parsed
     *
     * @return void
     */
    public function setParsedKey($key, $parsed)
    {
        $this->keyParserCache[$key] = $parsed;
    }

    /**
     * Parse a key into namespace, group, and item.
     *
     * @param string $key
     *
     * @return array
     */
    public function parseKey($key)
    {
        if (isset($this->keyParserCache[$key])) {
            return $this->keyParserCache[$key];
        }
        $segments = explode('.', $key);
        if (strpos($key, '::') === false) {
            $parsed = $this->keyParserParseBasicSegments($segments);
        } else {
            $parsed = $this->keyParserParseSegments($key);
        }
        return $this->keyParserCache[$key] = $parsed;
    }

    /**
     * Parse an array of basic segments.
     *
     * @param array $segments
     *
     * @return array
     */
    protected function keyParserParseBasicSegments(array $segments)
    {
        $group = $segments[0];
        if (count($segments) == 1) {
            return [null, $group, null];
        } else {
            $item = implode('.', array_slice($segments, 1));

            return [null, $group, $item];
        }
    }

    /**
     * Parse an array of namespaced segments.
     *
     * @param string $key
     *
     * @return array
     */
    protected function keyParserParseSegments($key)
    {
        list($namespace, $item) = explode('::', $key);
        $itemSegments = explode('.', $item);
        $groupAndItem = array_slice($this->keyParserParseBasicSegments($itemSegments), 1);

        return array_merge([$namespace], $groupAndItem);
    }
}
