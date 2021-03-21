<?php

declare(strict_types=1);

namespace Core\Service;

use Core\Entity\Database\BaseDbEntity;

class UuidGenerator
{
    public function generate(?BaseDbEntity $entity = null): string
    {
        if ($entity === null) {
            return $this->generateUuid();
        }

        return $this->generateUniqueUuid($entity);
    }

    private function generateUniqueUuid(BaseDbEntity $entity): string
    {
        do {
            $uuid = $this->generateUuid();

            $query = sprintf('uuid = \'%s\'', $uuid);
            $uuidExists = $entity::query()->whereQuery($query)->exists();
        } while ($uuidExists);

        return $this->generateUuid();
    }

    private function generateUuid(): string
    {
        // Generate 16 bytes (128 bits) of random data
        $data = random_bytes(16);
        assert(strlen($data) === 16);

        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // Output the 36 character UUID
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));

    }
}
