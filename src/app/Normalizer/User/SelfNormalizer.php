<?php

declare(strict_types=1);

namespace App\Normalizer\User;

use DB\Entity\User;

class SelfNormalizer
{
    public function normalize(User $user): array
    {
        $normalizedUser = $user->toArray();
        unset($normalizedUser['uuid']);

        return $normalizedUser;
    }
}
