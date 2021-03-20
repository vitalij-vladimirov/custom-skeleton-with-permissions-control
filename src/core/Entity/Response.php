<?php

declare(strict_types=1);

namespace Core\Entity;

class Response
{
    private array $content = [];
    private array $headers = [];
    private int $responseCode = 200;

    public function getContent(): array
    {
        return $this->content;
    }

    public function withContent(array $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function withHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    public function getResponseCode(): int
    {
        return $this->responseCode;
    }

    public function withResponseCode(int $responseCode): self
    {
        $this->responseCode = $responseCode;

        return $this;
    }
}
