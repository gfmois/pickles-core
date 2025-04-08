<?php

namespace Pickles\Http;

class Response {
    protected int $status = 200;
    protected array $headers = [];
    protected ?string $content = null;

    public function getStatus(): int {
        return $this->status;
    }

    public function setStatus(int $status): self {
        $this->status = $status;
        return $this;
    }

    public function getHedaers(): array {
        return $this->headers;
    }

    public function setHeader(HttpHeader $header, string $value): self {
        $this->headers[strtolower($header->value)] = $value;
        return $this;
    }

    public function removeHeader(HttpHeader $header): void {
        unset($this->headers[strtolower($header->value)]);
    }

    public function setHeaders(array $headers): self {
        $this->headers = $headers;
        return $this;
    }

    public function getContent(): ?string {
        return $this->content;
    }

    public function setContent(?string $content): self {
        $this->content = $content;
        return $this;
    }

    public function prepare() {
        $content = $this->getContent();
        if (is_null($content)) {
            $this->removeHeader(HttpHeader::CONTENT_TYPE);
            $this->removeHeader(HttpHeader::CONTENT_LENGTH);
        } else {
            $this->setHeader(HttpHeader::CONTENT_LENGTH, strlen($content));
        }
    }

    public function setContentType(string $content): self {
        $this->setHeader(HttpHeader::CONTENT_TYPE, $content);
        return $this;
    }

    public static function json(array $data): self {
        return (new self())
            ->setContentType("application/json")
            ->setContent(json_encode($data));
    }

    public static function text(string $text): self {
        return (new self())
            ->setContentType("text/plain")
            ->setContent($text);
    }

    public static function redirect(string $uri): self {
        return (new self())
            ->setStatus(302)
            ->setHeader(HttpHeader::LOCATION, $uri);
    }
}
