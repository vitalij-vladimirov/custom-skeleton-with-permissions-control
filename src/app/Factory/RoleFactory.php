<?php

declare(strict_types=1);

namespace App\Factory;

use DB\Entity\Role;

class RoleFactory
{
    public function create(string $identifier, string $title): Role
    {
        $role = new Role();
        $role->identifier = $identifier;
        $role->title = $title;

        return $role;
    }
}
