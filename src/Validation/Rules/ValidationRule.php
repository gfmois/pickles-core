<?php

namespace Pickles\Validation\Rules;

interface ValidationRule
{
    public function message(): string;
    public function validate(string $field, array $data): bool;
}
