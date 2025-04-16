<?php

namespace Pickles\Validation\Rules;

class Number implements ValidationRule
{
    public function message(): string
    {
        return "The :attribute must be a number.";
    }

    public function validate(string $field, array $data): bool
    {
        if (!array_key_exists($field, $data) || $data[$field] === "") {
            return false;
        }

        return is_numeric($data[$field]);
    }
}
