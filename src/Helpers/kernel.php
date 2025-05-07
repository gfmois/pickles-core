<?php

use Pickles\Config\Config;
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

/**
 * Retrieves the value of an environment variable.
 *
 * This function checks the `$_ENV` superglobal for the specified variable
 * and returns its value if it exists. If the variable is not set, the
 * provided default value will be returned instead.
 *
 * @param string $var The name of the environment variable to retrieve.
 * @param mixed $default The default value to return if the environment variable is not set. Defaults to null.
 * @return mixed The value of the environment variable, or the default value if the variable is not set.
 */
function env(string $var, mixed $default = null): mixed
{
    return $_ENV[$var] ?? $default;
}

/**
 * Retrieves the path to the resources directory.
 *
 * @return string The absolute path to the resources directory.
 */
function resourcesDirectory(): string
{
    return Kernel::$root . '/resources';
}

/**
 * Retrieve a configuration value by its key.
 *
 * This function fetches a configuration value from the application's configuration
 * repository. If the specified key does not exist, a default value can be returned.
 *
 * @param string $key The configuration key to retrieve.
 * @param mixed $default The default value to return if the key does not exist. Defaults to null.
 * @return mixed The configuration value associated with the key, or the default value if the key does not exist.
 */
function config(string $key, mixed $default = null): mixed
{
    return Config::get($key, $default);
}
