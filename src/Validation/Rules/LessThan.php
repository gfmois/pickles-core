<?php

namespace Pickles\Validation\Rules;

class LessThan implements ValidationRule
{
    private float $max;

    public function __construct(private float $lessThan)
    {
        $this->max = $lessThan;
    }

    public function message(): string
    {
        return "The value of :attribute must be less than {$this->max}.";
    }

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
