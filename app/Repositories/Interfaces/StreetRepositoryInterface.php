<?php

namespace App\Repositories\Interfaces;
use App\Models\Street;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Spatie\QueryBuilder\QueryBuilder;

interface StreetRepositoryInterface{

    /**
     * Search entities by filter
     *
     * @return LengthAwarePaginator|Collection|QueryBuilder[]
     */
    public function search(array $filter): Collection|LengthAwarePaginator|array;

    public function create(array $data): Street;

    public function update(Street $street, array $data): Street;

    public function delete(Street $street): void;
}