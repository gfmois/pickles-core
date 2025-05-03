<?php

namespace Pickles\Validation\Rules;

/**
 * Validation rule to check if a value is less than a specified threshold.
 *
 * This class implements the ValidationRule interface and provides
 * functionality to validate that a given value is less than a predefined
 * limit.
 *
 * @package PicklesFramework\Validation\Rules
 */
class LessThan implements ValidationRule
{
    private float $max;

    public function __construct(private float $lessThan)
    {
        $this->max = $lessThan;
    }

    /**
     * @inheritDoc
     */
    public function message(): string
    {
        return "The value of :attribute must be less than {$this->max}.";
    }


    /**
     * Validates that the value of a given field is less than a specified maximum value.
     *
     * @param string $field The name of the field to validate.
     * @param array $data The data array containing the field to validate.
     *
     * @return bool Returns true if the field exists in the data array, is numeric, and is less than the maximum value; otherwise, false.
     */
    public function validate(string $field, array $data): bool
    {
        if (!array_key_exists($field, $data)) {
            return false;
        }

        $fieldData = $data[$field];

        if ($fieldData === null || $fieldData === '' || !is_numeric($fieldData)) {
            return false;
        }

        return $fieldData < $this->max;
    }
}
