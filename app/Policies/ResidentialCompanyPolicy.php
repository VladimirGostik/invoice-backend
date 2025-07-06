<?php

namespace App\Policies;

use App\Enums\UserRoleEnum;
use App\Models\ResidentialCompany;
use App\Models\User;

class ResidentialCompanyPolicy
{
     public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([
            UserRoleEnum::SUPER_ADMIN,
            UserRoleEnum::ADMIN,
        ]);
    }

    public function view(User $user, ResidentialCompany $residentialCompany): bool
    {
        return $user->hasAnyRole([
            UserRoleEnum::SUPER_ADMIN,
            UserRoleEnum::ADMIN,
        ]) && $residentialCompany->company_type === 'residential';
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole([
            UserRoleEnum::SUPER_ADMIN,
        ]);
    }

    public function update(User $user, ResidentialCompany $residentialCompany): bool
    {
        return $user->hasAnyRole([
            UserRoleEnum::SUPER_ADMIN,
        ]) && $residentialCompany->company_type === 'residential';
    }

    public function delete(User $user, ResidentialCompany $residentialCompany): bool
    {
        return $user->hasAnyRole([
            UserRoleEnum::SUPER_ADMIN,
        ]) && $residentialCompany->company_type === 'residential';
    }
}
