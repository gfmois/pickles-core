<?php

namespace Pickles\Server;

use Pickles\Http\HttpHeader;
use Pickles\Http\HttpMethod;
use Pickles\Http\Request;
use Pickles\Http\Response;
use Pickles\Storage\File;

/**
 * Class PhpNativeServer
 *
 * Implementation of the Server interface using PHP's native global variables (`$_SERVER`, `$_POST`, `$_GET`).
 * Acts as a bridge between the application and the PHP environment, allowing access to the request data
 * and sending responses through native PHP functions.
 */
class PhpNativeServer implements Server
{
    /**
     * @inheritDoc
     */
    public function getRequest(): Request
    {
        return (new Request())
            ->setUri(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH))
            ->setMethod(HttpMethod::from($_SERVER["REQUEST_METHOD"]))
            ->setData($this->getRequestData())
            ->setQueryParams($_GET)
            ->setHeaders(getallheaders());
    }

    /**
     * @inheritDoc
     */
    public function sendResponse(Response $response): void
    {
        // PHP sends Content-Type header by default,
        // it has to be removed if the response has no content.
        $_header = HttpHeader::CONTENT_TYPE->value;

        // Content-Type header cannot be removed unless it is set to some value before
        header("$_header:None");
        header_remove(HttpHeader::CONTENT_TYPE->value);

        $response->prepare();
        http_response_code($response->getStatus());
        foreach ($response->getHeaders() as $header => $value) {
            header("$header:$value");
        }

        print($response->getContent());
    }

    /**
     * Retrieves uploaded files from the $_FILES superglobal and returns them as an array of File objects.
     *
     * Iterates over each entry in $_FILES, and for each file with a non-empty 'tmp_name',
     * creates a new File instance containing the file's contents, MIME type, and original name.
     *
     * @return array<string, File> An associative array of File objects, keyed by the input field name.
     */
    public function uploadedFiles(): array
    {
        $files = [];
        foreach ($_FILES as $key => $file) {
            if (!empty($file["tmp_name"])) {
                $files[$key] = new File(
                    file_get_contents($file["tmp_name"]),
                    $file["type"],
                    $file["name"],
                );
            }
        }

        return $files;
    }

    /**
     * Retrieves and parses the request data based on the request method and content type.
     *
     * - For POST requests with a non-JSON content type, returns the $_POST array.
     * - For requests with 'application/json' content type, decodes the JSON payload and returns it as an associative array.
     * - For other content types, parses the raw input and returns it as an array.
     *
     * @return array The parsed request data as an associative array.
     */
    public function getRequestData(): array
    {
        $headers = getallheaders();
        $isJson = isset($headers['Content-Type']) && strpos($headers['Content-Type'], 'application/json') !== false;

        if ($_SERVER["REQUEST_METHOD"] == "POST" && !$isJson) {
            return $_POST;
        }

        $data = file_get_contents("php://input");
        if ($isJson) {
            $decodedData = json_decode(json: $data, associative: true);
            return is_array($decodedData) ? $decodedData : [];
        } else {
            parse_str($data, $data);
        }

        return $data ?: [];
    }
}
