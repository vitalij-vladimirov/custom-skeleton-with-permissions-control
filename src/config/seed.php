<?php

return [
    \DB\Seed\UserAndRoleSeed::class => [
        \DB\Entity\User::class,
        \DB\Entity\Role::class,
        \DB\Entity\RolePermission::class,
        \DB\Entity\UserRole::class,
        \DB\Entity\UserPermission::class,
    ],
];
