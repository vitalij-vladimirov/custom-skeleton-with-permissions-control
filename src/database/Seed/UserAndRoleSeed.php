<?php

declare(strict_types=1);

namespace DB\Seed;

use App\Enum\Permission;
use App\Factory\RoleFactory;
use App\Factory\RolePermissionFactory;
use App\Factory\UserFactory;
use App\Factory\UserPermissionFactory;
use App\Factory\UserRoleFactory;
use Core\Service\Database\BaseSeed;
use DB\Entity\Role;
use DB\Entity\User;

class UserAndRoleSeed implements BaseSeed
{
    private const SYS_ADMIN_PERMISSIONS = [
        Permission::ROLE_VIEW,
        Permission::ROLE_CREATE,
        Permission::ROLE_UPDATE,
        Permission::ROLE_DELETE,
        Permission::ROLE_PERMISSION_VIEW,
        Permission::ROLE_PERMISSION_CREATE,
        Permission::ROLE_PERMISSION_UPDATE,
        Permission::ROLE_PERMISSION_DELETE,
    ];

    private const ADMIN_PERMISSIONS = [
        Permission::ROLE_VIEW,
        Permission::ROLE_PERMISSION_VIEW,
        Permission::USER_VIEW,
        Permission::USER_CREATE,
        Permission::USER_UPDATE,
        Permission::USER_DELETE,
        Permission::USER_ROLE_VIEW,
        Permission::USER_ROLE_CREATE,
        Permission::USER_ROLE_UPDATE,
        Permission::USER_ROLE_DELETE,
        Permission::USER_PERMISSION_VIEW,
        Permission::USER_PERMISSION_CREATE,
        Permission::USER_PERMISSION_UPDATE,
        Permission::USER_PERMISSION_DELETE,
    ];

    private const USER_PERMISSIONS = [
        Permission::USER_SELF_VIEW,
        Permission::USER_SELF_UPDATE,
    ];

    private const PASSWORD = 'Str0ngPassw0rd';

    private UserFactory $userFactory;
    private RoleFactory $roleFactory;
    private RolePermissionFactory $rolePermissionFactory;
    private UserRoleFactory $userRoleFactory;
    private UserPermissionFactory $userPermissionFactory;

    public function __construct(
        UserFactory $userFactory,
        RoleFactory $roleFactory,
        RolePermissionFactory $rolePermissionFactory,
        UserRoleFactory $userRoleFactory,
        UserPermissionFactory $userPermissionFactory
    ) {
        $this->userFactory = $userFactory;
        $this->roleFactory = $roleFactory;
        $this->rolePermissionFactory = $rolePermissionFactory;
        $this->userRoleFactory = $userRoleFactory;
        $this->userPermissionFactory = $userPermissionFactory;
    }

    public function run(): void
    {
        $users = $this->createUsers();
        $roles = $this->createRoles();

        $this->createRolePermissions($roles);
        $this->createUserRoles($users, $roles);
        $this->createUserPermissions($users);
    }

    /**
     * @return User[]
     */
    private function createUsers(): array
    {
        $users = [];

        $users['sys_admin'] = $this->userFactory
            ->create('Jim', 'Beam', 'sys_admin@example.com', self::PASSWORD)
            ->save();

        $users['admin'] = $this->userFactory
            ->create('Jack', 'Daniels', 'admin@example.com', self::PASSWORD)
            ->save();

        $users['user1'] = $this->userFactory
            ->create('Captain', 'Morgan', 'user1@example.com', self::PASSWORD)
            ->save();

        $users['user2'] = $this->userFactory
            ->create('Jose', 'Cuervo', 'user2@example.com', self::PASSWORD)
            ->save();

        return $users;
    }

    /**
     * @return Role[]
     */
    private function createRoles(): array
    {
        $roles = [];

        $roles['sys_admin'] = $this->roleFactory->create('sys_admin', 'System administrator')->save();
        $roles['admin'] = $this->roleFactory->create('admin', 'Administrator')->save();
        $roles['user'] = $this->roleFactory->create('user', 'User')->save();

        return $roles;
    }

    /**
     * @param Role[] $roles
     */
    public function createRolePermissions(array $roles): void
    {
        // System Administrator permissions
        foreach (self::SYS_ADMIN_PERMISSIONS as $permission) {
            $this->rolePermissionFactory->create($roles['sys_admin'], $permission)->save();
        }

        // Administrator permissions
        foreach (self::ADMIN_PERMISSIONS as $permission) {
            $this->rolePermissionFactory->create($roles['admin'], $permission)->save();
        }

        // User permissions
        foreach (self::USER_PERMISSIONS as $permission) {
            $this->rolePermissionFactory->create($roles['user'], $permission)->save();
        }
    }

    /**
     * @param User[] $users
     * @param Role[] $roles
     */
    private function createUserRoles(array $users, array $roles): void
    {
        // System Administrator has all roles
        $this->userRoleFactory->create($users['sys_admin'], $roles['sys_admin'])->save();
        $this->userRoleFactory->create($users['sys_admin'], $roles['admin'])->save();
        $this->userRoleFactory->create($users['sys_admin'], $roles['user'])->save();
        
        // Administrator has `admin` and `user` roles
        $this->userRoleFactory->create($users['admin'], $roles['admin'])->save();
        $this->userRoleFactory->create($users['admin'], $roles['user'])->save();
        
        // User has only `user` role
        $this->userRoleFactory->create($users['user1'], $roles['user'])->save();
        $this->userRoleFactory->create($users['user2'], $roles['user'])->save();
    }

    public function createUserPermissions(array $users): void
    {
        // This system admin is restricted to delete users
        $this->userPermissionFactory->create($users['sys_admin'], Permission::USER_DELETE, false)->save();

        // This user has additional permissions to view roles and users + create users
        $this->userPermissionFactory->create($users['user1'], Permission::ROLE_VIEW, true)->save();
        $this->userPermissionFactory->create($users['user1'], Permission::USER_VIEW, true)->save();
        $this->userPermissionFactory->create($users['user1'], Permission::USER_CREATE, true)->save();
    }
}
