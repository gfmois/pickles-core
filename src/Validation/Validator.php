<?php

namespace Pickles\Validation;

use Pickles\Validation\Exceptions\ValidationException;
use Pickles\Validation\Rules\ValidationRule;

/**
 * Class Validator
 *
 * This class is responsible for handling validation logic within the application.
 * It provides methods to validate data against specific rules and constraints.
 *
 * @package Validation
 */
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
     * @return array<string, mixed> An array containing the validated data. Fields that pass validation will be included.
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
                if (is_string($rule)) {
                    $rule = Rule::from($rule);
                }

                if (!$rule->validate($field, $this->data)) {
                    if (!$rule instanceof ValidationRule) {
                        throw new \InvalidArgumentException("Validation rule must implement ValidationRule interface.");
                    }

                    $ruleName = Rule::nameOf($rule);
                    $message = $messages[$field][$ruleName] ?? $rule->message();
                    $fieldErrors[$ruleName] = $message;
                }
            }

            if (count($fieldErrors) > 0) {
                $errors[$field] = $fieldErrors;
            } else {
                // $validated[$field] = $this->data[$field] ?? null;
                if (array_key_exists($field, $this->data)) {
                    $validated[$field] = $this->data[$field];
                }
            }
        }

        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }

        return $validated;
    }
}
