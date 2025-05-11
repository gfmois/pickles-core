<?php

namespace Pickles\Storage;

use Pickles\Storage\Drivers\FileStorageDriver;

/**
 * Stores the given content at the specified path using the FileStorageDriver.
 *
 * @param string $path The path where the content should be stored.
 * @param mixed $content The content to be stored. Can be of any type supported by the storage driver.
 * @return string The result of the storage operation, typically the path or identifier of the stored content.
 */
class Storage
{
    /**
     * Stores the given content at the specified path using the file storage driver.
     *
     * @param string $path The path where the content should be stored.
     * @param mixed $content The content to be stored. Can be of any type supported by the storage driver.
     * @return string The path where the content was stored.
     */
    public static function put(string $path, mixed $content): string
    {
        return app(FileStorageDriver::class)->put($path, $content);
    }
}
