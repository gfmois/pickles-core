<?php

namespace Pickles\Validation\Rules;

/**
 * Validation rule that checks if a field is required when another specified field is present.
 *
 * This rule ensures that the field under validation is not empty if any of the specified
 * fields are present in the input data.
 *
 * Implements the `ValidationRule` interface to define custom validation logic.
 */
class RequiredWith implements ValidationRule
{
    protected string $withField;

    public function __construct(string $withField)
    {
        $this->withField = $withField;
    }

    /**
     * @inheritDoc
     */
    public function message(): string
    {
        return "The field is required when :{$this->withField} is present.";
    }

    /**
     * Validates that the given field and the specified "with" field both have content.
     *
     * @param string $field The name of the field to validate.
     * @param array $data The data array containing the fields and their values.
     * @return bool Returns true if both the field and the "with" field have content; otherwise, false.
     */
    public function validate(string $field, array $data): bool
    {
        $fieldHasContent = fn ($fieldToCheck) => isset($data[$fieldToCheck]) && $data[$fieldToCheck] !== "";
        return $fieldHasContent($field) && $fieldHasContent($this->withField);
    }
}
