<?php

namespace Pickles\Storage\Drivers;

use Pickles\Exceptions\PicklesException;

/**
 * Class DiskFileStorage
 *
 * This class implements the FileStorageDriver interface and provides
 * functionality for storing and retrieving files on a disk-based storage system.
 *
 * @package PicklesFramework\Storage\Drivers
 */
class DiskFileStorage implements FileStorageDriver
{
    /**
     * @var string The directory path where files are stored on disk.
     */
    protected string $storageDirectory;

    /**
     * @var string The base URL of the application.
     */
    protected string $appUrl;

    /**
     * @var string The URI used to access the storage.
     */
    protected string $storageUri;

    public function __construct(string $storageDirectory, string $storageUri, string $appUrl)
    {
        $this->storageDirectory = $storageDirectory;
        $this->storageUri = $storageUri;
        $this->appUrl = $appUrl;
    }

    /**
     * Stores the given content at the specified path within the storage directory.
     *
     * This method ensures that the storage directory and any necessary subdirectories
     * are created before attempting to write the content to the file. If the operation
     * fails at any point, an exception is thrown.
     *
     * @param string $path The relative path (including the file name) where the content should be stored.
     * @param mixed $content The content to be written to the file. Can be a string or any serializable data.
     *
     * @throws PicklesException If the directory creation fails or the file cannot be written.
     *
     * @return string The URL to access the stored file.
     */
    public function put(string $path, mixed $content): string
    {
        if (!is_dir($this->storageDirectory)) {
            mkdir($this->storageDirectory, 0777, true);
        }

        $directories = explode("/", $path);
        $file = array_pop($directories);
        $dir = "$this->storageDirectory/";

        if (count($directories) > 0) {
            $dir = $this->storageDirectory . "/" . implode("/", $directories);
            if (!mkdir($dir, 0777, true) && !is_dir($dir)) {
                throw new PicklesException("Failed to create directory: $dir");
            }
        }

        if (!file_put_contents("$dir/$file", $content)) {
            throw new PicklesException("Failed to write content to file: $dir/$file. Please check file permissions and available disk space.");
        }

        return "$this->appUrl/$this->storageUri/$path";
    }
}
