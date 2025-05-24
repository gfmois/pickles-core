<?php

namespace Pickles\Config\Exceptions;

use Pickles\Exceptions\PicklesException;

/**
 * Exception thrown when a configuration file is invalid.
 *
 * @package PicklesFramework\Config\Exceptions
 */
class InvalidConfigFile extends PicklesException
{
    public function __construct(string $file)
    {
        parent::__construct("Invalid config file: $file");
    }
}
