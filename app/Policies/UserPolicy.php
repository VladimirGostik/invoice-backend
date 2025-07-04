<?php

namespace App\Policies;

use App\Enums\UserRoleEnum;
use App\Models\User;

class UserPolicy
{

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([
            UserRoleEnum::SUPER_ADMIN,
            UserRoleEnum::ADMIN,
        ]);
    }

    public function view(User $user, User $model): bool
    {
        return $user->hasAnyRole([
            UserRoleEnum::SUPER_ADMIN,
            UserRoleEnum::ADMIN,
        ]);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole([
            UserRoleEnum::SUPER_ADMIN,
        ]);
    }

    public function update(User $user, User $model): bool
    {
        return $user->hasAnyRole([
            UserRoleEnum::SUPER_ADMIN,
        ]);
    }

    public function delete(User $user, User $model): bool
    {
        return $user->hasAnyRole([
            UserRoleEnum::SUPER_ADMIN,
        ]);
    }
}