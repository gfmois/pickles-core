<?php

namespace Pickles\Validation;

use Pickles\Validation\Exceptions\ValidationException;
use Pickles\Validation\Rules\ValidationRule;

class Validator
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Validates the provided data against the given validation rules and returns the validated data.
     *
     * @param array<string, array<ValidationRule>> $validationRules An associative array where the keys are field names and the values are
     *                               either a single validation rule or an array of validation rules.
     * @param array $messages        An optional associative array of custom error messages. The keys should
     *                               be field names, and the values should be arrays where the keys are
     *                               validation rule class names and the values are the corresponding error messages.
     *
     * @return array An array containing the validated data. Fields that pass validation will be included.
     */
    public function validate(array $validationRules, array $messages = []): array
    {
        $errors = [];
        $validated = [];
        foreach ($validationRules as $field => $rules) {
            if (!is_array($rules)) {
                $rules = [$rules];
            }

            $fieldErrors = [];
            foreach ($rules as $rule) {
                if (!$rule->validate($field, $this->data)) {
                    if (!$rule instanceof ValidationRule) {
                        throw new \InvalidArgumentException("Validation rule must implement ValidationRule interface.");
                    }

                    $message = $messages[$field][$rule::class] ?? $rule->message();
                    $fieldErrors[$rule::class] = $message;
                }
            }

            if (count($fieldErrors) > 0) {
                $errors[$field] = $fieldErrors;
            } else {
                $validated[$field] = $this->data[$field];
            }
        }

        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }

        return $validated;
    }
}
