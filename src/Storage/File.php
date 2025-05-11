<?php

namespace Pickles\Storage;

/**
 * Class File
 *
 * This class is responsible for handling file storage operations.
 * It provides methods to interact with files, such as reading,
 * writing, and managing file data within the Pickles Framework.
 *
 * @package PicklesFramework\Storage
 */
class File
{
    public function __construct(
        private mixed $content,
        private string $type,
        private string $originalName
    ) {
        $this->originalName = $originalName;
        $this->type = $type;
        $this->content = $content;
    }

    /**
     * Determines if the file type represents an image.
     *
     * This method checks if the file's type starts with the string "image"
     * (case-insensitive), indicating that the file is an image.
     *
     * @return bool Returns true if the file type starts with "image", otherwise false.
     */
    public function isImg(): bool
    {
        return str_starts_with(strtolower($this->type), "image");
    }

    /**
     * Retrieves the file extension based on the MIME type.
     *
     * This method splits the MIME type string (e.g., "image/png") into its
     * components using the "/" delimiter and returns the second part, which
     * represents the file extension (e.g., "png").
     *
     * @return string The file extension derived from the MIME type. If the
     *                MIME type is invalid or does not contain a "/", null
     *                will be returned.
     */
    public function getExtension(): string
    {
        $parts = explode("/", $this->type);
        return $parts[1] ?? null;
    }

    /**
     * Stores the current file content in the specified directory or the default location.
     *
     * @param string|null $directory The directory where the file should be stored.
     *                               If null, the file will be stored in the default location.
     * @return string The path where the file was stored.
     */
    public function store(?string $directory = null): string
    {
        $file = uniqid() . $this->getExtension();
        $path = $directory === null
            ? $file
            : "$directory/$file";
        return Storage::put($path, $this->content);
    }
}
