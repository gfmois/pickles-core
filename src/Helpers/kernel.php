<?php

use Pickles\Container\Container;
use Pickles\Kernel;

function app(string $class = Kernel::class)
{
    return Container::resolve($class);
}

function singleton(string $class)
{
    if ($class === null || $class === "") {
        throw new InvalidArgumentException('Class name cannot be null or empty.');
    }

    return Container::singleton($class);
}
