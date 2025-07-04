<?php

namespace App\Repositories\Interfaces;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Spatie\QueryBuilder\QueryBuilder;

interface UserRepositoryInterface
{
    /**
     * Search users by filter
     *
     * @return LengthAwarePaginator|Collection|QueryBuilder[]
     */
    public function search(array $filter, bool $constraintToSelectedEntity): Collection|LengthAwarePaginator|array;

    /**
     * Create a new user
     */
    public function create(array $data): User;

    /**
     * Update a user
     */
    public function update(User $user, array $data): User;

    /**
     * Soft delete a user
     */
    public function delete(User $user): void;

    /**
     * Change the password of the user
     */
    public function changePassword(User $user, string $password): void;

}
