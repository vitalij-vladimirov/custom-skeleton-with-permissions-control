<?php

declare(strict_types=1);

namespace Core\Entity;

class Route
{
    public string $class;
    public string $method;
    public bool $hasIdentifier;
    public ?string $identifier = null;

    public static function create(string $class, string $method, bool $hasIdentifier = false): self
    {
        $instance = new self();

        $instance->class = $class;
        $instance->method = $method;
        $instance->hasIdentifier = $hasIdentifier;

        return $instance;
    }

    public function withIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }
}
