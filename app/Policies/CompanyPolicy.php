<?php

namespace App\Policies;

use App\Enums\CompanyTypeEnum;
use App\Enums\UserRoleEnum;
use App\Models\Company;
use App\Models\User;

class CompanyPolicy
{

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([
            UserRoleEnum::SUPER_ADMIN,
            UserRoleEnum::ADMIN,
        ]);
    }

    public function view(User $user, Company $company): bool
    {
        return $user->hasAnyRole([
            UserRoleEnum::SUPER_ADMIN,
            UserRoleEnum::ADMIN,
        ]) && $company->company_type === CompanyTypeEnum::MAIN;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole([
            UserRoleEnum::SUPER_ADMIN,
        ]);
    }

    public function update(User $user, Company $company): bool
    {
        return $user->hasAnyRole([
            UserRoleEnum::SUPER_ADMIN,
        ]) && $company->company_type === CompanyTypeEnum::MAIN;
    }

    public function updateCustomization(User $user, Company $company): bool
    {
        return $user->hasAnyRole([
            UserRoleEnum::SUPER_ADMIN,
        ]) && $company->company_type === CompanyTypeEnum::MAIN;
    }

    public function delete(User $user, Company $company): bool
    {
        return $user->hasAnyRole([
            UserRoleEnum::SUPER_ADMIN,
        ]) && $company->company_type === CompanyTypeEnum::MAIN;
    }
}