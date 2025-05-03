<?php

namespace Pickles\Database\Exceptions;

/**
 * Exception thrown when no fillable attributes are defined for a model.
 *
 * This exception is a specific type of `PicklesDatabaseException` and is used
 * to indicate that a model does not have any attributes marked as fillable,
 * which is required for mass assignment operations.
 *
 * @package PicklesFramework\Database\Exceptions
 */
class NoFillableAttributesDefinedException extends PicklesDatabaseException
{
}
