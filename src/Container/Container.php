<?php

namespace Pickles\Container;

/**
 * Class Container
 *
 * A simple dependency injection container that manages singleton instances of classes.
 */
class Container
{
    private static array $instances = [];


    /**
     * Retrieves or creates a singleton instance of the specified class.
     *
     * This method ensures that only one instance of the specified class is created
     * and reused throughout the application. If the instance does not already exist,
     * it will be created based on the provided build parameter.
     *
     * @param string $class The fully qualified class name of the singleton instance to retrieve or create.
     * @param string|callable|null $build Optional. A parameter to customize the instance creation:
     *                                    - If null, the class is instantiated with no arguments.
     *                                    - If a string, the class is instantiated with the string as a constructor argument.
     *                                    - If a callable, the callable is invoked to create the instance.
     * @return object The singleton instance of the specified class.
     *
     * @throws \InvalidArgumentException If the build parameter is of an invalid type.
     */
    public static function singleton(string $class, string|callable|null $build = null): object
    {
        if (!isset(self::$instances[$class])) {
            match (true) {
                ($build === null) =>  self::$instances[$class] = new $class(),
                is_string($build) => self::$instances[$class] = new $build(),
                is_callable($build) => self::$instances[$class] = $build(),
                default => throw new \InvalidArgumentException('Invalid build type provided.'),
            };
        }

        return self::$instances[$class];
    }

    /**
     * Resolves and retrieves an instance of the specified class from the container.
     *
     * @param string $className The fully qualified name of the class to resolve.
     * @return object|null The instance of the specified class if it exists in the container, or null if not found.
     */
    public static function resolve(string $className): ?object
    {
        return self::$instances[$className] ?? null;
    }
}
