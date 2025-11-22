<?php

namespace App\Http\Controllers;

use App\Http\Requests\MonthlyInvoice\StoreRequest;
use App\Http\Requests\MonthlyInvoice\UpdateRequest;
use App\Http\Requests\MonthlyInvoice\CreateOneTimeFromMonthlyRequest;
use App\Http\Resources\MonthlyInvoiceResource;
use App\Http\Resources\MonthlyInvoiceListResource;
use App\Models\MonthlyInvoice;
use App\Repositories\Interfaces\MonthlyInvoiceRepositoryInterface;
use App\Services\InvoiceService;
use Illuminate\Container\Attributes\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\UrlParam;

#[Group('Mesačné faktúry')]
class MonthlyInvoiceController extends Controller
{
    //use AuthorizesRequests;

    public function __construct(
        protected MonthlyInvoiceRepositoryInterface $repository,
        protected InvoiceService $service
    ) {}

    /**
     * Zoznam mesačných faktúr.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        //$this->authorize('viewAny', MonthlyInvoice::class);

        if (!$this->isUserSuperadmin()) {
            abort(403, 'Unauthorized');
        }

        $filters = $request->all();
        $collection = $this->repository->search($filters);
        return MonthlyInvoiceListResource::collection($collection);
    }

    /**
     * Zobrazenie jednej mesačnej faktúry.
     */
    #[UrlParam('monthlyInvoice', 'ID of the monthly invoice', example: 1)]
    public function show(MonthlyInvoice $monthlyInvoice): MonthlyInvoiceResource
    {
        if (!$this->isUserSuperadmin()) {
            abort(403, 'Unauthorized');
        }
        return new MonthlyInvoiceResource($monthlyInvoice);
    }

    /**
     * Vytvorenie novej mesačnej faktúry.
     */
    public function store(StoreRequest $request): JsonResponse
    {
        if (!$this->isUserSuperadmin()) {
            abort(403, 'Unauthorized');
        }
        //Log::debug('info data', $data = $request->all());
        $data = $request->validated();
        $invoice = $this->repository->create($data);
        return response()->json(['id' => $invoice->id], 201);
    }

    /**
     * Aktualizácia mesačnej faktúry.
     */
    #[UrlParam('monthlyInvoice', 'ID of the monthly invoice', example: 1)]
    public function update(UpdateRequest $request, MonthlyInvoice $monthlyInvoice): JsonResponse
    {
        if (!$this->isUserSuperadmin()) {
            abort(403, 'Unauthorized');
        }

        $data = $request->validated();
        $updated = $this->repository->update($monthlyInvoice, $data);
        return response()->json(['id' => $updated->id], 200);
    }

    // /**
    //  * Vytvorenie jednorazových faktúr z mesačnej faktúry.
    //  */
    // public function createOneTimeInvoices(CreateOneTimeFromMonthlyRequest $request): Response
    // {
    //     $this->authorize('create', MonthlyInvoice::class);
    //     $data = $request->validated();
    //     $this->service->createOneTimeInvoices($data);
    //     return response()->noContent();
    // }

    /**
     * Vymazanie mesačnej faktúry.
     */
    #[UrlParam('monthlyInvoice', 'ID of the monthly invoice', example: 1)]
    public function destroy(MonthlyInvoice $monthlyInvoice): JsonResponse
    {
        if (!$this->isUserSuperadmin()) {
            abort(403, 'Unauthorized');
        }

        $this->repository->delete($monthlyInvoice);
        return response()->json(null, 204);
    }
}
