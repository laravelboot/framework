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
namespace LaravelBoot\Foundation\Yaml\Validators;

use LaravelBoot\Foundation\Yaml\Exceptions\ValidationException;
use LaravelBoot\Foundation\Yaml\Loaders\YamlLoader;

/**
 * Class YamlValidator.
 */
class YamlValidator
{
    /**
     * The variables to validate.
     *
     * @var array
     */
    protected $variables;

    /**
     * The loader instance.
     *
     * @var \LaravelBoot\Foundation\Yaml\Loaders\YamlLoader
     */
    protected $loader;

    /**
     * Create a new validator instance.
     *
     * @param array                                      $variables
     * @param \LaravelBoot\Foundation\Yaml\Loaders\YamlLoader $loader
     */
    public function __construct(array $variables, YamlLoader $loader)
    {
        $this->variables = $variables;
        $this->loader = $loader;
        $this->assertCallback(
            function ($value) {
                return $value !== null;
            },
            'is missing'
        );
    }

    /**
     * Assert that each variable is not empty.
     *
     * @return \LaravelBoot\Foundation\Yaml\Validators\YamlValidator
     */
    public function notEmpty()
    {
        return $this->assertCallback(
            function ($value) {
                return strlen(trim($value)) > 0;
            },
            'is empty'
        );
    }

    /**
     * Assert that each specified variable is an integer.
     *
     * @return \LaravelBoot\Foundation\Yaml\Validators\YamlValidator
     */
    public function isInteger()
    {
        return $this->assertCallback(
            function ($value) {
                return ctype_digit($value);
            },
            'is not an integer'
        );
    }

    /**
     * Assert that each variable is amongst the given choices.
     *
     * @param string[] $choices
     *
     * @return \LaravelBoot\Foundation\Yaml\Validators\YamlValidator
     */
    public function allowedValues(array $choices)
    {
        return $this->assertCallback(
            function ($value) use ($choices) {
                return in_array($value, $choices);
            },
            'is not an allowed value'
        );
    }

    /**
     * Assert that the callback returns true for each variable.
     *
     * @param callable $callback
     * @param string   $message
     *
     * @throws \LaravelBoot\Foundation\Yaml\Exceptions\ValidationException
     *
     * @return \LaravelBoot\Foundation\Yaml\Validators\YamlValidator
     */
    private function assertCallback($callback, $message = 'failed callback assertion')
    {
        $variablesFailingAssertion = [];
        foreach ($this->variables as $variableName) {
            $variableValue = $this->loader->getEnvironmentVariable($variableName);
            if (call_user_func($callback, $variableValue) === false) {
                $variablesFailingAssertion[] = $variableName . " $message";
            }
        }
        if (count($variablesFailingAssertion) > 0) {
            throw new ValidationException(sprintf(
                'One or more environment variables failed assertions: %s.',
                implode(', ', $variablesFailingAssertion)
            ));
        }

        return $this;
    }
}
