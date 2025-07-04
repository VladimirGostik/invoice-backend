<?php

namespace App\Repositories;

use App\Enums\UserStateEnum;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Search users by filter
     *
     * @return LengthAwarePaginator|Collection|QueryBuilder[]
     */
    public function search(array $filter, bool $constraintToSelectedEntity = false): Collection|LengthAwarePaginator|array
    {
        // Build the query
        $query = QueryBuilder::for(User::class)
            //->resolveByRole()
            ->allowedFilters([
                'email',
                'first_name',
                'last_name',
                'phone',
                AllowedFilter::exact('state'),
            ])
            ->allowedSorts([
                'created_at',
                'email',
                'first_name',
                'last_name',
                'phone',
                'state',
            ]);

        // Constraint users to selected entity
//        if ($constraintToSelectedEntity) {
//            $query->whereHas('entities', fn($query) => $query->where('entities.id', app('entity')?->id));
//        }

        // Get pagination
        $paginate = (int)($filter['per_page'] ?? config('system.paginate'));

        return $paginate ?
            $query->paginate($paginate) :
            $query->get();
    }

    /**
     * Create a new user
     */
    public function create(array $data): User
    {
        // Set default user state
        $data['state'] = UserStateEnum::ACTIVE;
        $user = User::create($data);
        $user->assignRole($data['role']);

        return $user;

    }

    /**
     * Update a user
     */
    public function update(User $user, array $data): User
    {
        // Prevent password change
        // request except password and password confirmation
      //  unset($data['password']);

        // Prevent entity change
        // request->validated()
     //   unset($data['entity_id']);

        // Update properties
        $user->update($data);

        return $user;
    }

    /**
     * Soft delete a user
     */
    public function delete(User $user): void
    {
        $user->delete();
    }

    /**
     * Change the password of the user
     */
    public function changePassword(User $user, string $password): void
    {
        $user->password = Hash::make($password);
        $user->save();
    }

}