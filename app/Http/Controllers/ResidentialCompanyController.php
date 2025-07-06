<?php

namespace App\Http\Controllers;

use App\Enums\CompanyTypeEnum;
use App\Http\Requests\Company\StoreRequest;
use App\Http\Requests\Company\UpdateRequest;
use App\Http\Resources\CompanyResource;
use App\Models\ResidentialCompany;
use App\Repositories\Interfaces\CompanyRepositoryInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Knuckles\Scribe\Attributes\BodyParam;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\UrlParam;

#[Group('Bytové spoločnosti')]
class ResidentialCompanyController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private CompanyRepositoryInterface $companyRepo
    ) {}

    #[QueryParam('page', 'int', 'Set the page number for pagination. Default: 1', example: 1)]
    #[QueryParam('per_page', 'int', 'Set the number of records per page. Default: 10', example: 10)]
    #[QueryParam('filter[company_name]', 'string', 'Filter records by company_name.', example: 'Kegos s.r.o.')]
    public function index(Request $request)
    {
        $this->authorize('viewAny', ResidentialCompany::class);
        $filters = $request->all();
        $filters['filter'] = array_merge(
            $request->input('filter', []),
            ['company_type' => CompanyTypeEnum::RESIDENTIAL->value]
        );
        $collection = $this->companyRepo->search($filters);
        return CompanyResource::collection($collection);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $this->authorize('create', ResidentialCompany::class);
        $data = $request->validated();
        $data['company_type'] = CompanyTypeEnum::RESIDENTIAL->value;
        $company = $this->companyRepo->create($data);
        return response()->json(['id' => $company->id], 201);
    }

    public function show(ResidentialCompany $residentialCompany): CompanyResource
    {
        $this->authorize('view', $residentialCompany);
        return new CompanyResource($residentialCompany);
    }

    #[UrlParam('residentialCompany', 'ID of the residential company to update', example: 17)]
    public function update(UpdateRequest $request, ResidentialCompany $residentialCompany): CompanyResource
    {
        $this->authorize('update', $residentialCompany);
        $data = $request->validated();
        $company = $this->companyRepo->update($residentialCompany, $data);
        return new CompanyResource($company);
    }

    public function destroy(ResidentialCompany $residentialCompany): JsonResponse
    {
        $this->authorize('delete', $residentialCompany);
        $this->companyRepo->delete($residentialCompany);
        return response()->noContent();
    }
}