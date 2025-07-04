<?php

namespace App\Enums;

enum UserRoleEnum: string
{
    case SUPER_ADMIN = 'superadmin';
    case ADMIN = 'admin';
}
