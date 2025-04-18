<?php

namespace Pickles\Validation\Rules;

/**
 * Interface ValidationRule
 *
 * This interface defines the contract for validation rules in the application.
 * Each validation rule must implement the message and validate methods.
 */
interface ValidationRule
{
    /**
     * Returns the validation error message.
     *
     * @return string The error message indicating the field must be a number.
     */
    public function message(): string;

    /**
     * Validates the given field against the provided data.
     *
     * @param string $field The name of the field to validate.
     * @param array $data The data array containing the field and its value.
     * @return bool Returns true if the validation passes, otherwise false.
     */
    public function validate(string $field, array $data): bool;
}
