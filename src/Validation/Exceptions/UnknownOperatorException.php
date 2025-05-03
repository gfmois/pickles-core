<?php

namespace Pickles\Validation\Exceptions;

use Pickles\Exceptions\PicklesException;

/**
 * Exception thrown when an unknown operator is encountered during validation.
 *
 * This exception extends the base `PicklesException` and is used to indicate
 * that an invalid or unsupported operator was used in the validation process.
 */
class UnknownOperatorException extends PicklesException
{
    public function __construct(string $invalidOperator)
    {
        parent::__construct("Unknown operator: {$invalidOperator}");
    }
}
