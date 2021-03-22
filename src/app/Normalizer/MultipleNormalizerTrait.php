<?php

declare(strict_types=1);

namespace App\Normalizer;

trait MultipleNormalizerTrait
{
    public function normalizeMany($entities): array
    {
        return array_map([$this, 'normalize'], $entities);
    }
}
