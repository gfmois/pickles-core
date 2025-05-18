<?php

namespace Pickles\Container;

use Closure;
use InvalidArgumentException;
use Pickles\Database\Model;
use Pickles\Http\Exceptions\HttpNotFoundException;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;

class DependencyInjection
{
    public static function resolveParameters(Closure|array $callback, array $routeParameters = [])
    {
        if (!$callback instanceof Closure && !is_array($callback)) {
            throw new InvalidArgumentException("The callback must be a Closure or an array.");
        }
        if (is_array($callback)) {
            $class = $callback[0];
            $method = $callback[1];

            if (!class_exists($class::class)) {
                throw new InvalidArgumentException("Class $class does not exist.");
            }

            if (!method_exists($class, $method)) {
                throw new InvalidArgumentException("Method $method does not exist in class $class.");
            }

            $reflectionMethod = new ReflectionMethod($class, $method);
        } else {
            $reflectionMethod = new ReflectionFunction($callback);
        }

        $params = [];

        foreach ($reflectionMethod->getParameters() as $parameter) {
            $resolved = null;
            $className = $parameter->getType()?->getName();

            if (is_subclass_of($className, Model::class)) {
                $resolved = self::resolveModel($className, $routeParameters);
            } elseif ($parameter->getType()?->isBuiltin()) {
                $resolved = $routeParameters[$parameter->getName()] ?? null;
            } else {
                $resolved = app($className);
            }

            $params[] = $resolved;
        }

        return $params;
    }

    public static function resolveModel(string $className, array $routeParameters = []): Model
    {
        $modelClass = new ReflectionClass($className);
        $routeParamName = snake_case($modelClass->getShortName());
        $resolved = $className::find($routeParameters[$routeParamName] ?? 0);

        if ($resolved === null) {
            throw new HttpNotFoundException();
        }

        return $resolved;
    }
}
