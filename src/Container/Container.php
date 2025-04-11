<?php

namespace Pickles\Container;

class Container
{
    private static array $instances = [];

    public static function singleton(string $class): object
    {
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new $class();
        }

        return self::$instances[$class];
    }

    public static function resolve(string $className): ?object
    {
        return self::$instances[$className] ?? null;
    }
}
