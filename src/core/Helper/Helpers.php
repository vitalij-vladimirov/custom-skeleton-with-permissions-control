<?php

declare(strict_types=1);

if (!function_exists('isUuid')) {
    function isUuid(string $value): bool {
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $value) === 1;
    }
}
