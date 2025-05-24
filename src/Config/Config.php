<?php

namespace Pickles\Config;

use Pickles\Config\Exceptions\InvalidConfigFile;

/**
 * Class Config
 *
 * Handles the configuration settings for the application.
 * Provides methods to access and manage configuration options.
 */
class Config
{
    private static array $config = [];

    /**
     * Loads the configuration from the specified file path.
     *
     * @param string $path The path to the configuration file.
     * @return mixed The loaded configuration data.
     */
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

    /**
     * Retrieves the value of a specified configuration setting.
     *
     * @param string $configuration The name of the configuration setting to retrieve.
     * @param mixed $default The default value to return if the configuration setting is not found. Defaults to null.
     * @return mixed The value of the configuration setting, or the default value if not set.
     */
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
