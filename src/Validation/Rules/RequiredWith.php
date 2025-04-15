<?php

namespace Pickles\Validation\Rules;

class RequiredWith implements ValidationRule
{
    protected string $withField;

    public function __construct(string $withField)
    {
        $this->withField = $withField;
    }

    public function message(): string
    {
        return "The field is required when :{$this->withField} is present.";
    }

    public function validate(string $field, array $data): bool
    {
        $fieldHasContent = fn ($fieldToCheck) => isset($data[$fieldToCheck]) && $data[$fieldToCheck] !== "";
        return $fieldHasContent($field) && $fieldHasContent($this->withField);
    }
}
