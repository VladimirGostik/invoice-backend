<?php

namespace App\Repositories\Interfaces;
use App\Models\Company;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Spatie\QueryBuilder\QueryBuilder;

interface CompanyRepositoryInterface
{
    /**
     * Search entities by filter
     *
     * @return LengthAwarePaginator|Collection|QueryBuilder[]
     */
    public function search(array $filter): Collection|LengthAwarePaginator|array;

    public function create(array $data): Company;

    public function update(Company $company, array $data): Company;

    public function delete(Company $company): void;
}