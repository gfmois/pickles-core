<?php

namespace Pickles\Database\Exceptions;

/**
 * Exception thrown when no fillable attributes are provided for a database operation.
 *
 * This exception is a specific type of `PicklesDatabaseException` and is used to
 * indicate that the required fillable attributes for a database operation were not
 * specified, which may lead to an invalid or incomplete operation.
 *
 * @package PicklesFramework\Database\Exceptions
 */
class NoFillableAttributesProvidedException extends PicklesDatabaseException
{
}
