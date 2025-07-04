<?php

namespace App\Repositories;

use App\Models\AuditLog;
use App\Repositories\Interfaces\AuditLogRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AuditLogRepository implements AuditLogRepositoryInterface
{
    public function search(array $filter): Collection|LengthAwarePaginator|array
    {
        // Build the query
        $query = QueryBuilder::for(AuditLog::class)
            ->with('user')
            ->allowedFilters([
                AllowedFilter::exact('user_id'),
                AllowedFilter::exact('severity'),
                AllowedFilter::exact('http_status_code'),
                AllowedFilter::callback('from_created_at', function (Builder $query, $value) {
                    $query->where('created_at', '>=', $value);
                }),
                AllowedFilter::callback('to_created_at', function (Builder $query, $value) {
                    $query->where('created_at', '<=', $value);
                }),
                'action',
                'ip_address',
            ])
            ->allowedSorts([
                'created_at',
                'user_id',
                'severity',
                'http_status_code',
                'action',
                'ip_address',
            ]);

        // Get pagination
        $paginate = (int) ($filter['per_page'] ?? config('system.paginate'));

        return $paginate ?
            $query->paginate($paginate) :
            $query->get();
    }
}