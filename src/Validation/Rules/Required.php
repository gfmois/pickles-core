<?php

namespace Pickles\Validation\Rules;

class Required implements ValidationRule
{
    public function message(): string
    {
        return "The field is required.";
    }

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
