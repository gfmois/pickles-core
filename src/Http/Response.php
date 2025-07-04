<?php

namespace Pickles\Http;

use Constants;
use Pickles\Kernel;
use Pickles\View\Engine;
use Pickles\View\PicklesEngine;

/**
 * Class Response
 *
 * Represents an HTTP response to be sent to the client.
 * Allows setting status code, headers, and body content.
 * Includes helper methods for common response types (JSON, text, redirect).
 */
class Response
{
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
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
    * Set the HTTP status code.
    *
    * @param int $status
    * @return $this
    */
    public function setStatus(int $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get all headers set in the response.
     *
     * @param string $key Headers key
     * @return array<string, string>|string|null
     */
    public function getHeaders(?string $key = null): array|string|null
    {
        $headers = $this->headers;
        if ($key !== null) {
            return $headers[strtolower($key)] ?? null;
        }

        return $headers;
    }

    /**
     * Set a specific header with a value.
     *
     * @param HttpHeader|string $header The header name (enum or string).
     * @param string $value The header value.
     * @return $this
     */
    public function setHeader(HttpHeader|string $header, string $value): self
    {
        $headerKey = $header instanceof HttpHeader ? $header->value : $header;
        $this->headers[strtolower($headerKey)] = $value;
        return $this;
    }

    /**
     * Remove a specific header.
     *
     * @param HttpHeader $header The header to remove.
     * @return void
     */
    public function removeHeader(HttpHeader $header): void
    {
        unset($this->headers[strtolower($header->value)]);
    }

    /**
     * Replace all headers with a new set.
     *
     * @param array<string, string> $headers
     * @return $this
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * Get the response content.
     *
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Set the response content.
     *
     * @param string|null $content
     * @return $this
     */
    public function setContent(?string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Set the Content-Type header.
     *
     * @param string $content MIME type (e.g., application/json)
     * @return $this
     */
    public function setContentType(string $content): self
    {
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
    public function prepare()
    {
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
    public static function json(array $data): self
    {
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
    public static function text(string $text): self
    {
        return (new self())
            ->setContentType("text/plain")
            ->setContent($text . PHP_EOL);
    }

    /**
     * Create a redirect response to the given URI.
     *
     * @param string $uri The target URI to redirect to.
     * @return self
     */
    public static function redirect(string $uri): self
    {
        return (new self())
            ->setStatus(302)
            ->setHeader(HttpHeader::LOCATION, $uri);
    }

    /**
     * Creates a new instance of the current class with rendered HTML content from a view.
     *
     * This static factory method resolves the `Kernel` from the dependency container,
     * uses its view engine to render the specified view, and returns a new instance
     * with the rendered content and appropriate content type.
     *
     * @param string      $view   The name of the view to render (without .php extension).
     * @param array       $params An associative array of parameters to be extracted into the view scope.
     * @param string|null $layout Optional layout name. If null, the engine's default layout is used.
     *
     * @return self A new instance of the class with HTML content set.
     *
     * @throws \RuntimeException If the resolved Kernel instance is not of the expected type.
     */
    public static function view(string $view, array $params = [], ?string $layout = null): self
    {
        $viewEngine = app(Engine::class);
        if (!$viewEngine instanceof Engine) {
            throw new \RuntimeException("Resolved instance is not of type Engine.");
        }

        $content = $viewEngine->render($view, $params, $layout);
        return (new self())
            ->setContentType("text/html")
            ->setContent($content);
    }

    /**
     * Attach error messages and old input data to the session and set the HTTP status code.
     *
     * This method is typically used to handle validation errors or other error scenarios
     * where you want to provide feedback to the user and preserve their input data.
     *
     * @param array $errors An array of error messages to be flashed to the session.
     * @param int $status The HTTP status code to set for the response. Defaults to 400.
     * @return self Returns the current instance for method chaining.
     */
    public function withErrors(array $errors, int $status = 400): self
    {
        $this->setStatus($status);
        session()->flash(Constants::ERRORS_KEY, $errors);
        session()->flash(Constants::OLD_DATA_KEY, request()->data());

        return $this;
    }
}
