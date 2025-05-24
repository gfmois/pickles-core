<?php

namespace Pickles\Container\Exceptions;

/**
 * Exception thrown when a resolved instance is not a valid Authenticator.
 *
 * This exception extends the PicklesContainerException and is used to indicate
 * that an invalid instance has been saved or resolved within the container.
 */
class InvalidInstanceSavedException extends PicklesContainerException
{
    public function __construct()
    {
        parent::__construct("The resolved instance is not a valid Authenticator.");
    }
}
