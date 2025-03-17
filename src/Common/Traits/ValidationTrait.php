<?php

namespace CrazyCodeGen\Common\Traits;

use CrazyCodeGen\Common\Exceptions\InvalidIdentifierFormatException;
use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Common\Models\ConversionRule;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

trait ValidationTrait
{
    private null|PropertyAccessorInterface $propertyAccess = null;

    /**
     * @throws InvalidIdentifierFormatException
     */
    public function assertIsValidIdentifier(string $name): void
    {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $name)) {
            throw new InvalidIdentifierFormatException($name);
        }
    }

    /**
     * @param mixed $value
     * @param ConversionRule[] $rules
     * @return mixed
     * @throws NoValidConversionRulesMatchedException
     */
    public function convertOrThrow(mixed $value, array $rules): mixed
    {
        $ruleMatched = false;
        foreach ($rules as $rule) {
            if ($rule->inputType === 'null' && is_null($value)) {
                $ruleMatched = true;
                break;
            } elseif ($rule->inputType === 'string' && is_string($value)) {
                if (!is_null($rule->outputType)) {
                    $value = new $rule->outputType($value);
                }
                $ruleMatched = true;
                break;
            } elseif ($rule->inputType === 'array' && is_array($value)) {
                if (!is_null($rule->outputType)) {
                    if (isset($rule->filter)) {
                        $value = array_filter($value, $rule->filter);
                    }
                    $value = new $rule->outputType($value);
                }
                $ruleMatched = true;
                break;
            } elseif (is_object($value) && (is_subclass_of($value, $rule->inputType) || get_class($value) === $rule->inputType)) {
                if (!is_null($rule->outputType) && !empty($rule->propertyPath)) {
                    $properties = [];
                    foreach ($rule->propertyPaths as $propertyPath) {
                        if (is_null($this->propertyAccess)) {
                            $this->propertyAccess = PropertyAccess::createPropertyAccessor();
                        }
                        $properties[] = $this->propertyAccess->getValue($value, $propertyPath);
                    }
                    $value = new $rule->outputType($properties);
                } elseif (!is_null($rule->outputType)) {
                    $value = new $rule->outputType($value);
                }
                $ruleMatched = true;
                break;
            }
        }
        if (!$ruleMatched) {
            throw new NoValidConversionRulesMatchedException();
        }
        return $value;
    }

    /**
     * @param array $values
     * @param ConversionRule[] $rules
     * @return array
     */
    public function convertAndDropNonCompliantValues(array $values, array $rules): array
    {
        foreach ($values as $key => $value) {
            try {
                $values[$key] = $this->convertOrThrow($value, $rules);
            } catch (NoValidConversionRulesMatchedException) {
                unset($values[$key]);
            }
        }
        return $values;
    }
}
