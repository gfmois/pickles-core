<?php

namespace Pickles\Config;

use Pickles\Config\Exceptions\InvalidConfigFile;

class Config
{
    private static array $config = [];

    public static function load(string $path)
    {
        foreach (glob("$path/*.php") as $file) {
            $key = explode(".", basename($file))[0];
            $values = require_once $file;

            if (is_array($values)) {
                self::$config[$key] = $values;
            } else {
                throw new InvalidConfigFile($file);
            }
        }
    }

    public static function get(string $configuration, mixed $default = null)
    {
        $keys = explode(".", $configuration);
        $finalKey = array_pop($keys);
        $array = self::$config;

        foreach ($keys as $key) {
            if (!array_key_exists($key, $array)) {
                return $default;
            }

            $array = $array[$key];
        }

        return $array[$finalKey] ?? $default;
    }
}
