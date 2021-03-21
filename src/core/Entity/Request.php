<?php

declare(strict_types=1);

namespace Core\Entity;

class Request
{
    /** @var string[] */
    private array $content;
    /** @var string[] */
    private array $headers;
    /** @var mixed[]  */
    private array $params = [];

    public function __construct(array $content, array $headers)
    {
        $this->content = $content;
        $this->headers = $headers;
    }

    public function getContent(): array
    {
        return $this->content;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getHeader($key): ?string
    {
        return $this->headers[$key] ?? null;
    }

    public function getParams(): array
    {
        return array_merge($this->params, $this->content);
    }

    /**
     * @param $key
     *
     * @return object|array|string|int|float|bool|null
     */
    public function getParam($key)
    {
        return $this->params[$key] ?? $this->content[$key] ?? null;
    }

    public function setParams(array $params): self
    {
        $this->params = $params;

        return $this;
    }

    public function addParam(string $key, $param): self
    {
        $this->params[$key] = $param;

        return $this;
    }

}
