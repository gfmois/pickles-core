<?php

namespace Pickles\Validation\Rules;

class Min implements ValidationRule
{
    private int $min;

    public function __construct(private int $minLength)
    {
        $this->min = $minLength;
    }

    /**
     * @inheritDoc
     */
    public function message(): string
    {
        return "The field must be at least {$this->min} characters.";
    }

    public function validate(string $field, array $data): bool
    {
        $value = $data[$field] ?? null;
        if ($value === null || $value === '') {
            return false;
        }

        $value = trim($value);
        return strlen($value) >= $this->min;
    }
}
