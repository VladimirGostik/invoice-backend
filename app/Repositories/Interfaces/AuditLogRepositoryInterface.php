<?php

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Spatie\QueryBuilder\QueryBuilder;

interface AuditLogRepositoryInterface
{
    /**
     * Search audit logs by filter
     *
     * @return LengthAwarePaginator|Collection|QueryBuilder[]
     */
    public function search(array $filter): Collection|LengthAwarePaginator|array;
}