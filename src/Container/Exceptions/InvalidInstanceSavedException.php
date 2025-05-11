<?php

namespace Pickles\Container\Exceptions;

class InvalidInstanceSavedException extends PicklesContainerException
{
    public function __construct()
    {
        parent::__construct("The resolved instance is not a valid Authenticator.");
    }
}
