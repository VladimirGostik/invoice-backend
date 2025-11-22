<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;
use App\Enums\UserRoleEnum;

class CompanyPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // ✅ Priama role kontrola - žiadne extra dotazy
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Company $company): bool
    {
        // ✅ Superadmin môže všetko
        if ($user->role === UserRoleEnum::SUPER_ADMIN) {
            return true;
        }

        // ✅ Admin môže vidieť len svoje firmy
        if ($user->role === UserRoleEnum::ADMIN) {
            return $user->company_id === $company->id;
        }

        // ✅ User nemôže vidieť companies
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === UserRoleEnum::SUPER_ADMIN;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Company $company): bool
    {
        if ($user->role === UserRoleEnum::SUPER_ADMIN) {
            return true;
        }

        return $user->company_id === $company->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Company $company): bool
    {
        // Len superadmin môže mazať
        return $user->role === UserRoleEnum::SUPER_ADMIN;
    }

    /**
     * ✅ Specific policy pre residential companies
     */
    public function viewResidential(User $user): bool
    {
        // Len superadmin môže vidieť residential companies
        return $user->role === UserRoleEnum::SUPER_ADMIN;
    }

    /**
     * ✅ Specific policy pre customization
     */
    public function updateCustomization(User $user, Company $company): bool
    {
        if ($user->role === UserRoleEnum::SUPER_ADMIN) {
            return true;
        }

        return $user->company_id === $company->id;
    }
}
