<?php

namespace App\Http\Controllers;

use App\Enums\CompanyTypeEnum;
use App\Http\Requests\Company\StoreRequest;
use App\Http\Requests\Company\UpdateRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Repositories\Interfaces\CompanyRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Knuckles\Scribe\Attributes\BodyParam;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\UrlParam;

#[Group('Firmy')]
class CompanyController extends Controller
{
    use AuthorizesRequests;
    private CompanyRepositoryInterface $companyRepo;

    public function __construct(CompanyRepositoryInterface $companyRepo)
    {
        $this->companyRepo = $companyRepo;
    }

    #[QueryParam('page', 'int', 'Set the page number for pagination. Default: 1', example: 1)]
    #[QueryParam('per_page', 'int', 'Set the number of records per page. Default: 10', example: 10)]
    #[QueryParam('filter[company_name]', 'string', 'Filter records by company_name.', example: 'Kegos s.r.o.')]
    public function index(Request $request)
    {
        $this->authorize('viewAny', Company::class);
        $filters = $request->all();
        $filters['filter'] = array_merge(
            $request->input('filter', []),
            ['company_type' => CompanyTypeEnum::MAIN->value]
        );
        $collection = $this->companyRepo->search($filters);
        return CompanyResource::collection($collection);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $this->authorize('create', Company::class);

        $data = $request->validated();
        $data['company_type'] = CompanyTypeEnum::MAIN->value;
        $company = $this->companyRepo->create($data);
        return response()->json(['id' => $company->id], 201);
    }

    public function show(Company $company): CompanyResource
    {
        $this->authorize('view', $company);
        return new CompanyResource($company);
    }

    #[UrlParam('company', 'ID of the company to update', example: 1)]
    public function update(UpdateRequest $request, Company $company): CompanyResource
    {
        $this->authorize('update', $company);
        $company = $this->companyRepo->update($company, $request->validated());
        return new CompanyResource($company);
    }

    public function destroy(Company $company): JsonResponse
    {
        $this->authorize('delete', $company);
        $this->companyRepo->delete($company);
        return response()->noContent();
    }
}