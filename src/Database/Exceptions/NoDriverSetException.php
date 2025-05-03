<?php

namespace Pickles\Database\Exceptions;

/**
 * Exception thrown when no database driver is set in the application.
 *
 * This exception is used to indicate that a required database driver
 * has not been configured or initialized, which is necessary for
 * database operations to proceed.
 *
 * @package PicklesFramework\Database\Exceptions
 */
class NoDriverSetException extends PicklesDatabaseException
{
}
