<?php

namespace Pickles\Config\Exceptions;

use Pickles\Exceptions\PicklesException;

class InvalidConfigFile extends PicklesException
{
    public function __construct(string $file)
    {
        parent::__construct("Invalid config file: $file");
    }
}
