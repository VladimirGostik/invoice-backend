<?php

namespace App\Repositories;

use App\Models\Company;
use App\Models\Street;
use App\Repositories\Interfaces\StreetRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class StreetRepository implements StreetRepositoryInterface
{

    public function search(array $filter): Collection|LengthAwarePaginator|array
    {
        request()->merge($filter);
        
        $query = QueryBuilder::for(Street::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('street_name'),
            ]);

        return $query->paginate();
    }

    public function create(array $data): Street
    {
        return Street::create($data);
    }

    public function update(Street $street, array $data): Street
    {
        $street->update($data);
        return $street;
    }

    public function delete(Street $street): void
    {
        $street->delete();
    }
}