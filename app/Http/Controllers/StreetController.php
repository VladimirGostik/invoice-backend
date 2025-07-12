<?php

namespace App\Http\Controllers;

use App\Http\Requests\Street\StoreRequest;
use App\Http\Requests\Street\UpdateRequest;
use App\Http\Resources\StreetResource;
use App\Models\Street;
use App\Repositories\Interfaces\StreetRepositoryInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;


#[Group('Ulice')]
class StreetController extends Controller
{
    use AuthorizesRequests;

    private StreetRepositoryInterface $streetRepo;

    public function __construct(StreetRepositoryInterface $streetRepo) {
        $this->streetRepo = $streetRepo;
    }

    #[QueryParam('page', 'int', 'Set the page number for pagination. Default: 1', example: 1)]
    #[QueryParam('per_page', 'int', 'Set the number of records per page. Default: 10', example: 10)]
    #[QueryParam('filter[street_name]', 'string', 'Filter records by street_name.', example: 'Bebravska 39')]
    #[QueryParam('filter[company_id]', 'string', 'Filter records by company_id.', example: '1')]
    public function index(Request $request)
    {
        $this->authorize('viewAny', Street::class);
        $filters = $request->all();
        $collection = $this->streetRepo->search($filters);
        return StreetResource::collection($collection);
    }

    public function show(Request $request, Street $street)
    {
        $this->authorize('view', $street);
        return new StreetResource($street);
    }

    public function store(StoreRequest $request)
    {
        $this->authorize('create', Street::class);
        $data = $request->validated();
        $street = $this->streetRepo->create($data);
        return response()->json(['id' => $street->id], 201);
    }

    public function update(UpdateRequest $request, Street $street)
    {
        $this->authorize('update', $street);
        $data = $request->validated();
        $updatedStreet = $this->streetRepo->update($street, $data);
        return response()->json(['id' => $updatedStreet->id], 200);
    }

    public function destroy(Street $street)
    {
        $this->authorize('delete', $street);
        $this->streetRepo->delete($street);
        return response()->json(null, 204);
    }
}
