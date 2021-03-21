<?php

declare(strict_types=1);

namespace App\Normalizer;

use DB\Entity\User;

class UserNormalizer
{
    public function normalize(User $user): array
    {
        $normalizedUser = $user->toArray();
        unset($normalizedUser['uuid']);

        return $normalizedUser;
    }
}
