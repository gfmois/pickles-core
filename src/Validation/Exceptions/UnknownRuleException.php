<?php

namespace Pickles\Validation\Exceptions;

use Pickles\Exceptions\PicklesException;

/**
 * Exception thrown when an unknown validation rule is encountered.
 *
 * This exception is used to indicate that a validation rule specified
 * in the application is not recognized or does not exist.
 *
 * @package PicklesFramework\Validation\Exceptions
 */
class UnknownRuleException extends PicklesException
{
}
