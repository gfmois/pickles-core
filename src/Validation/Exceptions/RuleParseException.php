<?php

namespace Pickles\Validation\Exceptions;

use Pickles\Exceptions\PicklesException;

/**
 * Exception thrown when there is an error parsing a validation rule.
 *
 * This exception is a specialized type of `PicklesException` and is used
 * to indicate issues specifically related to the parsing of validation rules
 * within the Pickles Framework.
 */
class RuleParseException extends PicklesException
{
}
