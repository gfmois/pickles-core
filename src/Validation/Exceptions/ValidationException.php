<?php

namespace Pickles\Validation\Exceptions;

use Pickles\Exceptions\PicklesException;

/**
 * Class ValidationException
 *
 * Represents an exception that is thrown when a validation error occurs.
 * This class extends the base PicklesException to provide more specific
 * context for validation-related errors within the Pickles Framework.
 *
 * @package PicklesFramework\Validation\Exceptions
 */
class ValidationException extends PicklesException
{
    protected array $errors = [];

    public function __construct(array $errors = [])
    {
        $this->errors = $errors;
    }

    /**
     * Retrieves the validation errors associated with the exception.
     *
     * @return array An array of validation error messages.
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
