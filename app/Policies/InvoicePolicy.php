<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;
use App\Enums\UserRoleEnum;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([
            UserRoleEnum::SUPER_ADMIN,
            UserRoleEnum::ADMIN,
        ]);
    }

    public function view(User $user, Invoice $invoice): bool
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

    public function update(User $user, Invoice $invoice): bool
    {
        return $user->hasAnyRole([
            UserRoleEnum::SUPER_ADMIN,
        ]);
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        return $user->hasAnyRole([
            UserRoleEnum::SUPER_ADMIN,
        ]);
    }
}