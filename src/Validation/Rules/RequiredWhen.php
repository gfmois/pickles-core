<?php

namespace Pickles\Validation\Rules;

class RequiredWhen implements ValidationRule
{
    private string $otherField;
    private string $operator;
    private string $compareWith;

    public function __construct(
        string $otherField,
        string $operator,
        string $compareWith
    ) {
        $this->otherField = $otherField;
        $this->operator = $operator;
        $this->compareWith = $compareWith;
    }

    public function message(): string
    {
        return "Field required when: {$this->otherField} {$this->operator} {$this->compareWith}.";
    }

    /**
     * Validates a field based on the presence and value of another field in the data array.
     *
     * @param string $field The name of the field to validate.
     * @param array $data The data array containing the fields and their values.
     *
     * @return bool Returns true if the field is valid based on the condition; otherwise, false.
     *
     * The validation checks if the `otherField` exists in the data array. If it does, it evaluates
     * the condition specified by the operator (`=`, `>`, `<`, `>=`, `<=`, `!=`) between the value
     * of `otherField` and `compareWith`. If the condition is met, it checks if the `$field` exists
     * in the data array and is not empty after trimming.
     */
    public function validate(string $field, array $data): bool
    {
        if (!array_key_exists($this->otherField, $data)) {
            return false;
        }

        $otherFieldData = $data[$this->otherField];
        $isConditionMet = match ($this->operator) {
            "=" => $otherFieldData == $this->compareWith,
            ">" => $otherFieldData > floatval($this->compareWith),
            "<" => $otherFieldData < floatval($this->compareWith),
            ">=" => $otherFieldData >= floatval($this->compareWith),
            "<=" => $otherFieldData <= floatval($this->compareWith),
            "!=" => $otherFieldData != floatval($this->compareWith),
            default => false,
        };

        return !$isConditionMet || isset($data[$field]) && $data[$field] != "";
    }
}
