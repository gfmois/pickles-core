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
     * Retrieves a singleton instance of the specified class.
     *
     * This method ensures that only one instance of the given class is created
     * and reused throughout the application. If an instance of the class does
     * not already exist, it will be instantiated and stored.
     *
     * @param string $class The fully qualified class name of the object to retrieve.
     * @return object The singleton instance of the specified class.
     */
    public static function singleton(string $class): object
    {
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new $class();
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
