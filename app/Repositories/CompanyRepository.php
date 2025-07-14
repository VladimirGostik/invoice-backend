<?php

namespace App\Repositories;

use App\Models\Company;
use App\Repositories\Interfaces\CompanyRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CompanyRepository implements CompanyRepositoryInterface
{
    public function search(array $filter): Collection|LengthAwarePaginator|array
    {
        request()->merge($filter);

        $query = QueryBuilder::for(Company::class)
            ->allowedFilters([
                'company_name',
                AllowedFilter::exact('company_type'), 
            ]);
            
        // Get pagination
        $paginate = (int)($filter['per_page'] ?? config('system.paginate'));

        return $paginate ?
            $query->paginate($paginate) :
            $query->get();
    }

    public function create(array $data): Company
    {
        return Company::create($data);
    }

    public function update(Company $company, array $data): Company
    {
        $company->update($data);
        return $company;
    }

    public function delete(Company $company): void
    {
        $company->delete();
    }

    public function updateCustomization(Company $company, array $data): void
    {
        $company->companyCustomization()->updateOrCreate([], $data);
    }
}