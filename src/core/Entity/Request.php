<?php

declare(strict_types=1);

namespace Core\Entity;

class Request
{
    /** @var string[] */
    private array $content;
    /** @var string[] */
    private array $headers;
    /** @var string[] */
    private array $params = [];

    public function __construct(array $content, array $headers)
    {
        $this->content = $content;
        $this->headers = $headers;
    }

    public function setContent(array $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getHeader($key): ?string
    {
        return $this->headers[$key];
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getParam($key): ?string
    {
        return $this->params[$key] ?? $this->content[$key] ?? null;
    }

    public function setParams(array $params): self
    {
        $this->params = $params;

        return $this;
    }

    public function addParam(string $key, string $param): self
    {
        $this->params[$key] = $param;

        return $this;
    }

}
