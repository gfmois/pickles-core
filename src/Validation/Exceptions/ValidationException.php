<?php

namespace Pickles\Validation\Exceptions;

use Pickles\Exceptions\PicklesException;

class ValidationException extends PicklesException
{
    protected array $errors = [];

    public function __construct(array $errors = [])
    {
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
