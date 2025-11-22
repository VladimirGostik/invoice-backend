<?php

namespace App\Enums;

enum UserRoleEnum: string
{
    case SUPER_ADMIN = 'superadmin';
    case ADMIN = 'admin';
    case USER = 'user';

    public function isSuperadmin(): bool
    {
        return $this === self::SUPER_ADMIN;
    }

    public function isAdmin(): bool
    {
        return in_array($this, [self::SUPER_ADMIN, self::ADMIN]);
    }

    public function getLabel(): string
    {
        return match($this) {
            self::SUPER_ADMIN => 'Super Admin',
            self::ADMIN => 'Admin',
            self::USER => 'User',
        };
    }
}
