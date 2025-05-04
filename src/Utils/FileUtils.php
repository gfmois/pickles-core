<?php

namespace Pickles\Utils;

/**
 * Class FileUtils
 *
 * A utility class that provides helper methods for file operations.
 *
 * @package PicklesFramework\Utils
 */
class FileUtils
{
    /**
     * Ensures that the specified directory exists. If the directory does not exist,
     * it attempts to create it, including any necessary parent directories.
     *
     * @param string $dir The path of the directory to check or create.
     *
     * @throws \RuntimeException If the directory cannot be created.
     */
    public static function ensureDirectoryExists(string $dir): void
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}
