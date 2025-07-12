<?php

namespace App\Policies;

use App\Enums\UserRoleEnum;
use App\Models\Street;
use App\Models\User;

class StreetPolicy
{

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([
            UserRoleEnum::SUPER_ADMIN,
            UserRoleEnum::ADMIN,
        ]);
    }

    public function view(User $user, Street $street): bool
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

    public function update(User $user, Street $street): bool
    {
        return $user->hasAnyRole([
            UserRoleEnum::SUPER_ADMIN,
        ]);
    }

    public function delete(User $user, Street $street): bool
    {
        return $user->hasAnyRole([
            UserRoleEnum::SUPER_ADMIN,
        ]);
    }
}