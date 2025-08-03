<?php

namespace App\Http\Controllers;

use App\Http\Requests\Invoice\StoreRequest;
use App\Http\Resources\OneTimeInvoiceResource;
use App\Models\Company;
use App\Models\Invoice;
use App\Repositories\Interfaces\InvoiceRepositoryInterface;
use App\Services\InvoiceService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\UrlParam;

#[Group('Faktúry')]
class InvoiceController extends Controller
{
    use AuthorizesRequests;

    protected InvoiceRepositoryInterface $invoiceRepo;
    protected InvoiceService $invoiceService;

    public function __construct(
        InvoiceRepositoryInterface $invoiceRepo,
        InvoiceService $invoiceService
    ) {
        $this->invoiceRepo = $invoiceRepo;
        $this->invoiceService = $invoiceService;
    }

    /**
     * Zoznam všetkých faktúr.
     */
    public function searchOneTime(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Invoice::class);
        $filters = $request->all();
        $collection = $this->invoiceRepo->searchOneTime($filters);
        return OneTimeInvoiceResource::collection($collection);
    }

    /**
     * Zobrazenie jednej faktúry.
     */
    #[UrlParam('invoice', 'ID of the invoice', example: 1)]
    public function view(Invoice $invoice): OneTimeInvoiceResource
    {
        $this->authorize('view', $invoice);
        return new OneTimeInvoiceResource($invoice);
    }

    /**
     * Zoznam mesačných faktúr.
     */
    public function indexMonthly(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Invoice::class);
        $filters = $request->all();
        $collection = $this->invoiceRepo->searchMonthly($filters);
        return OneTimeInvoiceResource::collection($collection);
    }

    /**
     * Získa posledné číslo faktúry pre firmu.
     */
    #[UrlParam('company_id', 'ID of the company', example: 1)]
    public function getLastInvoiceNumber(int $company_id, int $billing_year): JsonResponse
    {
        $this->authorize('viewAny', Invoice::class);

        $lastNumber = $this->invoiceService->getLastInvoiceNumber($company_id, $billing_year);
        return response()->json(['last_invoice_number' => $lastNumber ?? 'Žiadna faktúra']);
    }

    /**
     * Vytvorenie novej faktúry.
     */
    public function storeOneTime(StoreRequest $request): JsonResponse
    {
        $this->authorize('create', Invoice::class);
        $data = $request->validated();
        $invoice = $this->invoiceService->createInvoice($data);
        return response()->json(new OneTimeInvoiceResource($invoice), 201);
    }

    /**
     * Vymazanie faktúry.
     */
    #[UrlParam('invoice', 'ID of the invoice to delete', example: 1)]
    public function delete(Invoice $invoice): JsonResponse
    {
        $this->authorize('delete', $invoice);
        $this->invoiceRepo->delete($invoice);
        return response()->json(null, 204);
    }

    // public function storeMonthly(StoreRequest $request): JsonResponse
    // {
    //     $this->authorize('create', Invoice::class);

    //     $data = $request->validated();
    //     $company = Company::find($data['company_id']);
    //     $data['invoice_number'] = $this->invoiceService->generate($company);

    //     $invoice = $this->invoiceRepo->createMonthly($data);
    //     return response()->json(new MonthlyInvoiceResource($invoice), 201);
    // }

}