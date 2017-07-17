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
namespace LaravelBoot\Foundation\Configuration\Writers;

use Exception;

/**
 * Class ConfigWriter.
 */
class ConfigurationWriter
{
    /**
     * Save a configuration value change to file.
     *
     * @param      $filePath
     * @param      $newValues
     * @param bool $useValidation
     *
     * @return mixed|string
     * @throws \Exception
     */
    public function toFile($filePath, $newValues, $useValidation = true)
    {
        $contents = file_get_contents($filePath);
        $contents = $this->toContent($contents, $newValues, $useValidation);
        file_put_contents($filePath, $contents);

        return $contents;
    }

    /**
     * Save a configuration value change to contents.
     *
     * @param      $contents
     * @param      $newValues
     * @param bool $useValidation
     *
     * @return mixed
     * @throws \Exception
     */
    public function toContent($contents, $newValues, $useValidation = true)
    {
        $contents = $this->parseContent($contents, $newValues);
        if (!$useValidation) {
            return $contents;
        }
        $result = eval('?>' . $contents);
        foreach ($newValues as $key => $expectedValue) {
            $parts = explode('.', $key);
            $array = $result;
            foreach ($parts as $part) {
                if (!is_array($array) || !array_key_exists($part, $array)) {
                    throw new Exception(sprintf('Unable to rewrite key "%s" in config, does it exist?', $key));
                }
                $array = $array[$part];
            }
            $actualValue = $array;
            if ($actualValue != $expectedValue) {
                throw new Exception(sprintf('Unable to rewrite key "%s" in config, rewrite failed', $key));
            }
        }

        return $contents;
    }

    /**
     * Parsing new values from contents.
     *
     * @param $contents
     * @param $newValues
     *
     * @return mixed
     */
    protected function parseContent($contents, $newValues)
    {
        $patterns = [];
        $replacements = [];
        foreach ($newValues as $path => $value) {
            $items = explode('.', $path);
            $key = array_pop($items);
            $replaceValue = $this->writeValueToPhp($value);
            $patterns[] = $this->buildStringExpression($key, $items);
            $replacements[] = '${1}${2}' . $replaceValue;
            $patterns[] = $this->buildStringExpression($key, $items, '"');
            $replacements[] = '${1}${2}' . $replaceValue;
            $patterns[] = $this->buildConstantExpression($key, $items);
            $replacements[] = '${1}${2}' . $replaceValue;
            $patterns[] = $this->buildArrayExpression($key, $items);
            $replacements[] = '${1}${2}' . $replaceValue;
        }

        return preg_replace($patterns, $replacements, $contents, 1);
    }

    /**
     * Save a configuration value change to php code.
     *
     * @param $value
     *
     * @return array|mixed|string
     */
    protected function writeValueToPhp($value)
    {
        if (is_string($value) && strpos($value, "'") === false) {
            $replaceValue = "'" . $value . "'";
        } elseif (is_string($value) && strpos($value, '"') === false) {
            $replaceValue = '"' . $value . '"';
        } elseif (is_bool($value)) {
            $replaceValue = ($value ? 'true' : 'false');
        } elseif (is_null($value)) {
            $replaceValue = 'null';
        } elseif (is_array($value) && count($value) === count($value, COUNT_RECURSIVE)) {
            $replaceValue = $this->writeArrayToPhp($value);
        } else {
            $replaceValue = $value;
        }
        $replaceValue = str_replace('$', '\$', $replaceValue);

        return $replaceValue;
    }

    /**
     * Save a array format configuration value change to php code.
     *
     * @param array $array
     *
     * @return string
     */
    protected function writeArrayToPhp($array)
    {
        $result = [];
        foreach ($array as $value) {
            if (!is_array($value)) {
                $result[] = $this->writeValueToPhp($value);
            }
        }

        return '[' . implode(', ', $result) . ']';
    }

    /**
     * Build a string expression from key-value.
     *
     * @param        $targetKey
     * @param array  $arrayItems
     * @param string $quoteChar
     *
     * @return string
     */
    protected function buildStringExpression($targetKey, $arrayItems = [], $quoteChar = "'")
    {
        $expression = [];
        $expression[] = $this->buildArrayOpeningExpression($arrayItems);
        $expression[] = '([\'|"]' . $targetKey . '[\'|"]\s*=>\s*)[' . $quoteChar . ']';
        $expression[] = '([^' . $quoteChar . ']*)';
        $expression[] = '[' . $quoteChar . ']';

        return '/' . implode('', $expression) . '/';
    }

    /**
     * Build a constant expression from key-value.
     *
     * @param       $targetKey
     * @param array $arrayItems
     *
     * @return string
     */
    protected function buildConstantExpression($targetKey, $arrayItems = [])
    {
        $expression = [];
        $expression[] = $this->buildArrayOpeningExpression($arrayItems);
        $expression[] = '([\'|"]' . $targetKey . '[\'|"]\s*=>\s*)';
        $expression[] = '([tT][rR][uU][eE]|[fF][aA][lL][sS][eE]|[nN][uU][lL]{2}|[\d]+)';

        return '/' . implode('', $expression) . '/';
    }

    /**
     * Build a array expression from key-value.
     *
     * @param       $targetKey
     * @param array $arrayItems
     *
     * @return string
     */
    protected function buildArrayExpression($targetKey, $arrayItems = [])
    {
        $expression = [];
        $expression[] = $this->buildArrayOpeningExpression($arrayItems);
        $expression[] = '([\'|"]' . $targetKey . '[\'|"]\s*=>\s*)';
        $expression[] = '(?:[aA][rR]{2}[aA][yY]\(|[\[])([^\]|)]*)[\]|)]';

        return '/' . implode('', $expression) . '/';
    }

    /**
     * Build a array opening expression from key-value.
     *
     * @param array $arrayItems
     *
     * @return string
     */
    protected function buildArrayOpeningExpression($arrayItems)
    {
        if (count($arrayItems)) {
            $itemOpen = [];
            foreach ($arrayItems as $item) {
                $itemOpen[] = '[\'|"]' . $item . '[\'|"]\s*=>\s*(?:[aA][rR]{2}[aA][yY]\(|[\[])';
            }
            $result = '(' . implode('[\s\S]*', $itemOpen) . '[\s\S]*?)';
        } else {
            $result = '()';
        }

        return $result;
    }
}
