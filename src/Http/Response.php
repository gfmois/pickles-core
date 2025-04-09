<?php

namespace Pickles\Http;

/**
 * Class Response
 * 
 * Represents an HTTP response to be sent to the client.
 * Allows setting status code, headers, and body content.
 * Includes helper methods for common response types (JSON, text, redirect).
 */
class Response {
    /**
     * The HTTP status code of the response (default: 200 OK).
     *
     * @var int
     */
    protected int $status = 200;

    /**
     * HTTP headers to be sent with the response.
     * Keys are header names (lowercase), and values are header values.
     *
     * @var array<string, string>
     */
    protected array $headers = [];

    /**
     * The body content of the response.
     *
     * @var string|null
     */
    protected ?string $content = null;

    /**
     * Get the current HTTP status code.
     *
     * @return int
     */
    public function getStatus(): int {
        return $this->status;
    }

     /**
     * Set the HTTP status code.
     *
     * @param int $status
     * @return $this
     */
    public function setStatus(int $status): self {
        $this->status = $status;
        return $this;
    }

    /**
     * Get all headers set in the response.
     *
     * @return array<string, string>
     */
    public function getHedaers(): array {
        return $this->headers;
    }

    /**
     * Set a specific header with a value.
     *
     * @param HttpHeader $header The header name (enum).
     * @param string $value The header value.
     * @return $this
     */
    public function setHeader(HttpHeader $header, string $value): self {
        $this->headers[strtolower($header->value)] = $value;
        return $this;
    }

    /**
     * Remove a specific header.
     *
     * @param HttpHeader $header The header to remove.
     * @return void
     */
    public function removeHeader(HttpHeader $header): void {
        unset($this->headers[strtolower($header->value)]);
    }

    /**
     * Replace all headers with a new set.
     *
     * @param array<string, string> $headers
     * @return $this
     */
    public function setHeaders(array $headers): self {
        $this->headers = $headers;
        return $this;
    }

    /**
     * Get the response content.
     *
     * @return string|null
     */
    public function getContent(): ?string {
        return $this->content;
    }

    /**
     * Set the response content.
     *
     * @param string|null $content
     * @return $this
     */
    public function setContent(?string $content): self {
        $this->content = $content;
        return $this;
    }

    /**
     * Set the Content-Type header.
     *
     * @param string $content MIME type (e.g., application/json)
     * @return $this
     */
    public function setContentType(string $content): self {
        $this->setHeader(HttpHeader::CONTENT_TYPE, $content);
        return $this;
    }

    /**
     * Prepare the response by adjusting headers based on content presence.
     * If content is null, removes Content-Type and Content-Length.
     * Otherwise, sets Content-Length based on content length.
     *
     * @return void
     */
    public function prepare() {
        $content = $this->getContent();
        if (is_null($content)) {
            $this->removeHeader(HttpHeader::CONTENT_TYPE);
            $this->removeHeader(HttpHeader::CONTENT_LENGTH);
        } else {
            $this->setHeader(HttpHeader::CONTENT_LENGTH, strlen($content));
        }
    }

    /**
     * Create a JSON response.
     *
     * @param array $data The data to encode as JSON.
     * @return self
     */
    public static function json(array $data): self {
        return (new self())
            ->setContentType("application/json")
            ->setContent(json_encode($data));
    }

    /**
     * Create a plain text response.
     *
     * @param string $text The text content.
     * @return self
     */
    public static function text(string $text): self {
        return (new self())
            ->setContentType("text/plain")
            ->setContent($text);
    }

    /**
     * Create a redirect response to the given URI.
     *
     * @param string $uri The target URI to redirect to.
     * @return self
     */
    public static function redirect(string $uri): self {
        return (new self())
            ->setStatus(302)
            ->setHeader(HttpHeader::LOCATION, $uri);
    }
}
