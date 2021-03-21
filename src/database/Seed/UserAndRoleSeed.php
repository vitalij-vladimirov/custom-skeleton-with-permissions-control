<?php

declare(strict_types=1);

namespace DB\Seed;

use App\Enum\Permission;
use Core\Service\Database\BaseSeed;
use App\Service\PasswordManager;
use DB\Entity\Role;
use DB\Entity\RolePermission;
use DB\Entity\User;
use DB\Entity\UserPermission;
use DB\Entity\UserRole;

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

    private PasswordManager $passwordManager;

    public function __construct(PasswordManager $passwordManager)
    {
        $this->passwordManager = $passwordManager;
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

        $user = new User();
        $user->firstName = 'Jim';
        $user->lastName = 'Beam';
        $user->email = 'sys_admin@example.com';
        $user->password = $this->passwordManager->hashPassword(self::PASSWORD);
        $users['sys_admin'] = $user->save();

        $user = new User();
        $user->firstName = 'Jack';
        $user->lastName = 'Daniels';
        $user->email = 'admin@example.com';
        $user->password = $this->passwordManager->hashPassword(self::PASSWORD);
        $users['admin'] = $user->save();

        $user = new User();
        $user->firstName = 'Captain';
        $user->lastName = 'Morgan';
        $user->email = 'user1@example.com';
        $user->password = $this->passwordManager->hashPassword(self::PASSWORD);
        $users['user1'] = $user->save();

        $user = new User();
        $user->firstName = 'Jose';
        $user->lastName = 'Cuervo';
        $user->email = 'user2@example.com';
        $user->password = $this->passwordManager->hashPassword(self::PASSWORD);
        $users['user2'] = $user->save();

        return $users;
    }

    /**
     * @return Role[]
     */
    private function createRoles(): array
    {
        $roles = [];

        $role = new Role();
        $role->identifier = 'sys_admin';
        $role->title = 'System administrator';
        $roles['sys_admin'] = $role->save();

        $role = new Role();
        $role->identifier = 'admin';
        $role->title = 'Administrator';
        $roles['admin'] = $role->save();

        $role = new Role();
        $role->identifier = 'user';
        $role->title = 'User';
        $roles['user'] = $role->save();

        return $roles;
    }

    /**
     * @param Role[] $roles
     */
    public function createRolePermissions(array $roles): void
    {
        // System Administrator permissions
        foreach (self::SYS_ADMIN_PERMISSIONS as $permission) {
            $rolePermission = new RolePermission();
            $rolePermission->roleId = $roles['sys_admin']->id;
            $rolePermission->permission = $permission;
            $rolePermission->save();
        }

        // Administrator permissions
        foreach (self::ADMIN_PERMISSIONS as $permission) {
            $rolePermission = new RolePermission();
            $rolePermission->roleId = $roles['admin']->id;
            $rolePermission->permission = $permission;
            $rolePermission->save();
        }

        // User permissions
        foreach (self::USER_PERMISSIONS as $permission) {
            $rolePermission = new RolePermission();
            $rolePermission->roleId = $roles['user']->id;
            $rolePermission->permission = $permission;
            $rolePermission->save();
        }
    }

    /**
     * @param User[] $users
     * @param Role[] $roles
     */
    private function createUserRoles(array $users, array $roles): void
    {
        // System Administrator has all roles
        $userRole = new UserRole();
        $userRole->userId = $users['sys_admin']->id;
        $userRole->roleId = $roles['sys_admin']->id;
        $userRole->save();
        
        $userRole = new UserRole();
        $userRole->userId = $users['sys_admin']->id;
        $userRole->roleId = $roles['admin']->id;
        $userRole->save();
        
        $userRole = new UserRole();
        $userRole->userId = $users['sys_admin']->id;
        $userRole->roleId = $roles['user']->id;
        $userRole->save();
        
        // Administrator has `admin` and `user` roles
        $userRole = new UserRole();
        $userRole->userId = $users['admin']->id;
        $userRole->roleId = $roles['admin']->id;
        $userRole->save();
        
        $userRole = new UserRole();
        $userRole->userId = $users['admin']->id;
        $userRole->roleId = $roles['user']->id;
        $userRole->save();
        
        // User has only `user` role
        $userRole = new UserRole();
        $userRole->userId = $users['user1']->id;
        $userRole->roleId = $roles['user']->id;
        $userRole->save();
        
        $userRole = new UserRole();
        $userRole->userId = $users['user2']->id;
        $userRole->roleId = $roles['user']->id;
        $userRole->save();
    }

    public function createUserPermissions(array $users): void
    {
        // This system admin is restricted to delete users
        $userPermission = new UserPermission();
        $userPermission->userId = $users['sys_admin']->id;
        $userPermission->permission = Permission::USER_DELETE;
        $userPermission->grant = false;
        $userPermission->save();

        // This user has additional permissions to view roles and users + create users
        $userPermission = new UserPermission();
        $userPermission->userId = $users['user1']->id;
        $userPermission->permission = Permission::ROLE_VIEW;
        $userPermission->grant = true;
        $userPermission->save();

        $userPermission = new UserPermission();
        $userPermission->userId = $users['user1']->id;
        $userPermission->permission = Permission::USER_VIEW;
        $userPermission->grant = true;
        $userPermission->save();

        $userPermission = new UserPermission();
        $userPermission->userId = $users['user1']->id;
        $userPermission->permission = Permission::USER_CREATE;
        $userPermission->grant = true;
        $userPermission->save();
    }
}
