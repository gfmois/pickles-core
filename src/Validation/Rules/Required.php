<?php

namespace Pickles\Validation\Rules;

/**
 * Class Required
 *
 * This class implements the ValidationRule interface and provides a rule
 * to validate that a specific field is required and contains a non-empty value.
 *
 * @package Validation\Rules
 */
class Required implements ValidationRule
{
    /**
     * @inheritDoc
     */
    public function message(): string
    {
        return "The field is required.";
    }

    /**
     * Validates whether the specified field exists in the data array and is not empty.
     *
     * @param string $field The name of the field to validate.
     * @param array $data The data array containing the field and its value.
     * @return bool True if the field exists and is not empty, false otherwise.
     */
    public function validate(string $field, array $data): bool
    {
        $hasContent = isset($data[$field]);

        # if value stored in $data[$field] is string  trim($data[$field]) !== ''
        if ($hasContent && is_string($data[$field])) {
            $hasContent = trim($data[$field]) !== '';
        }

        return $hasContent;
    }
}
