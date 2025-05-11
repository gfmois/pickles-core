<?php

namespace Pickles\Storage\Drivers;

/**
 * Interface FileStorageDriver
 *
 * Defines the contract for a file storage driver, which is responsible
 * for handling file storage operations.
 */
interface FileStorageDriver
{
    /**
     * Stores the given content at the specified path.
     *
     * @param string $path The file path where the content should be stored.
     * @param mixed $content The content to be stored. Can be of any type.
     * @return string Returns the path where the content was stored.
     */
    public function put(string $path, mixed $content): string;
}
