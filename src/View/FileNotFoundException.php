<?php

namespace Pickles\View;

use Pickles\Exceptions\PicklesException;

/**
 * Exception thrown when a requested file is not found.
 *
 * This exception extends the base `PicklesException` and is used to indicate
 * that a specific file required by the application could not be located.
 *
 * @package PicklesFramework\View
 */
class FileNotFoundException extends PicklesException
{
}
