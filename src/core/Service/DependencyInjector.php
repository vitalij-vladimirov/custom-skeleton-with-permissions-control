<?php

declare(strict_types=1);

namespace Core\Service;

use ReflectionClass;

class DependencyInjector
{
    public function inject(string $class): object
    {
        $reflection = new ReflectionClass($class);

        $constructor = $reflection->getConstructor();

        if ($constructor === null || $constructor->getNumberOfParameters() === 0) {
            return new $class();
        }

        $constructorParameters = $constructor->getParameters();
        $resolvedParameters = $this->resolveClassParameters($constructorParameters);

        return new $class(... array_values($resolvedParameters));
    }

    private function resolveClassParameters(array $constructorParameters): array
    {
        if (count($constructorParameters) === 0) {
            return [];
        }

        $parameters = [];
        foreach ($constructorParameters as $constructorParameter) {
            if ($constructorParameter->getClass() === null) {
                $parameters[] = $constructorParameter->getDefaultValue();

                continue;
            }

            $parameters[] = $this->inject(
                $constructorParameter->getClass()->getName()
            );
        }

        return $parameters;
    }
}
