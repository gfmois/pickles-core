<?php

use Pickles\Container\Container;
use Pickles\Kernel;

/**
 * Retrieve an instance of the specified class from the container.
 *
 * By default, this function resolves the `Kernel` class, but you can specify
 * a different class to resolve by passing its fully qualified class name.
 *
 * @param string $class The fully qualified class name to resolve. Defaults to `Kernel::class`.
 * @return mixed The resolved instance of the specified class.
 */
function app(string $class = Kernel::class)
{
    return Container::resolve($class);
}

/**
 * Retrieves (or creates new one if doesn't exists) a singleton instance of the specified class from the container.
 *
 * @param string $class The fully qualified class name to retrieve as a singleton.
 *
 * @throws InvalidArgumentException If the class name is null or empty.
 *
 * @return mixed The singleton instance of the specified class.
 */
function singleton(string $class)
{
    if ($class === null || $class === "") {
        throw new InvalidArgumentException('Class name cannot be null or empty.');
    }

    return Container::singleton($class);
}
