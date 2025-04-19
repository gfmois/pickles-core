<?php

namespace Pickles\Validation\Exceptions;

use Pickles\Exceptions\PicklesException;

class UnknownOperatorException extends PicklesException
{
    public function __construct(string $invalidOperator)
    {
        parent::__construct("Unknown operator: {$invalidOperator}");
    }
}
