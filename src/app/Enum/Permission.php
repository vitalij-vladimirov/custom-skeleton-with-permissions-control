<?php

declare(strict_types=1);

namespace App\Enum;

use Core\Service\Enum;

class Permission extends Enum
{
    public const USER_SELF_VIEW = 'user_self_view';
    public const USER_SELF_UPDATE = 'user_self_update';
    
    public const USER_VIEW = 'user_view';
    public const USER_CREATE = 'user_create';
    public const USER_UPDATE = 'user_update';
    public const USER_DELETE = 'user_delete';

    public const ROLE_VIEW = 'role_view';
    public const ROLE_CREATE = 'role_create';
    public const ROLE_UPDATE = 'role_update';
    public const ROLE_DELETE = 'role_delete';

    public const ROLE_PERMISSION_VIEW = 'role_permission_view';
    public const ROLE_PERMISSION_CREATE = 'role_permission_create';
    public const ROLE_PERMISSION_UPDATE = 'role_permission_update';
    public const ROLE_PERMISSION_DELETE = 'role_permission_delete';

    public const USER_ROLE_VIEW = 'user_role_view';
    public const USER_ROLE_CREATE = 'user_role_create';
    public const USER_ROLE_UPDATE = 'user_role_update';
    public const USER_ROLE_DELETE = 'user_role_delete';

    public const USER_PERMISSION_VIEW = 'user_permission_view';
    public const USER_PERMISSION_CREATE = 'user_permission_create';
    public const USER_PERMISSION_UPDATE = 'user_permission_update';
    public const USER_PERMISSION_DELETE = 'user_permission_delete';
}
