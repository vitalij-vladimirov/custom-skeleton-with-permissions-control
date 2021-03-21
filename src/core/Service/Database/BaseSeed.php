<?php

declare(strict_types=1);

namespace Core\Service\Database;

interface BaseSeed
{
    public function run(): void;
}
