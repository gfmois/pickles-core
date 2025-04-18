<?php

namespace Pickles\Validation\Rules;

/**
 * Class Number
 *
 * This class implements a validation rule to check if a given field's value is a number.
 */
class Number implements ValidationRule
{
    /**
     * @inheritDoc
     */
    public function message(): string
    {
        return "The :attribute must be a number.";
    }

    /**
     * Validates whether the given field in the data array is a number.
     *
     * @param string $field The name of the field to validate.
     * @param array $data The data array containing the field to validate.
     * @return bool True if the field exists and its value is numeric, false otherwise.
     */
    public function validate(string $field, array $data): bool
    {
        if (!array_key_exists($field, $data) || $data[$field] === "") {
            return false;
        }

        return is_numeric($data[$field]);
    }
}
