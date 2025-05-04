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
 * Registers or retrieves a singleton instance of a class in the container.
 *
 * This function ensures that only one instance of the specified class is created
 * and shared throughout the application. If a callable or a specific build logic
 * is provided, it will be used to construct the instance.
 *
 * @param string $class The fully qualified class name to register or retrieve as a singleton.
 * @param string|callable|null $build Optional. A callable or a string representing the logic
 *                                    to build the instance. If null, the default constructor
 *                                    will be used.
 *
 * @throws InvalidArgumentException If the class name is null or an empty string.
 *
 * @return mixed The singleton instance of the specified class.
 */
function singleton(string $class, string|callable|null $build = null)
{
    if ($class === null || $class === "") {
        throw new InvalidArgumentException('Class name cannot be null or empty.');
    }

    return Container::singleton($class, $build);
}
