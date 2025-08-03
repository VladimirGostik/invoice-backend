<?php

namespace App\Repositories;

use App\Models\Company;
use App\Repositories\Interfaces\CompanyRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

use function Psy\debug;

class CompanyRepository implements CompanyRepositoryInterface
{
    public function searchResidential(array $filter): Collection|LengthAwarePaginator|array
    {
        // Log the $type value
        $query = QueryBuilder::for(Company::where('company_type', 'residential'))
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
        $companies = Company::where('company_type', 'main');
        $query = QueryBuilder::for($companies)
                    ->allowedFilters([
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