<?php

namespace App\Repositories;

use App\Models\Company;
use App\Repositories\Interfaces\CompanyRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Spatie\QueryBuilder\QueryBuilder;


class CompanyRepository implements CompanyRepositoryInterface
{
    public function searchResidential(array $filter): Collection|LengthAwarePaginator|array
    {
        // Log the $type value
        $query = QueryBuilder::for(Company::class)
            ->ResidentialCompany()
            ->allowedFilters([
            'company_name',
            ]);
        // Get pagination
        $paginate = (int)($filter['per_page'] ?? config('system.paginate'));

        return $paginate ?
            $query->paginate($paginate) :
            $query->get();
    }

    public function searchMain(array $filter): Collection|LengthAwarePaginator|array
    {
        $query = QueryBuilder::for(Company::class)
            ->MainCompany()
            ->with(['companyCustomization', 'signatures'])
            ->allowedFilters([
                'company_name',
            ])->allowedSorts([
                'company_name',
                    ]);
                // Get pagination
                $paginate = (int)($filter['per_page'] ?? config('system.paginate'));

                return $paginate ?
                    $query->paginate($paginate) :
                    $query->get();
    }

    public function create(array $data, string $type): Company
    {
        $data['company_type'] = $type;
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