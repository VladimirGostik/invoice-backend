<?php

namespace App\Http\Controllers;

use App\Http\Requests\Invoice\StoreRequest;
use App\Http\Requests\Invoice\UpdateRequest;
use App\Http\Resources\OneTimeInvoiceResource;
use App\Http\Resources\OneTimeInvoiceListResource;
use App\Models\Invoice;
use App\Repositories\Interfaces\InvoiceRepositoryInterface;
use App\Services\InvoiceService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\UrlParam;


#[Group('Jednorazové faktúry')]
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
     * Zoznam jednorazovych faktúr
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        if (!$this->isUserSuperadmin()) {
            abort(403, 'Unauthorized');
        }
        $filters = $request->all();
        $collection = $this->invoiceRepo->search($filters);
        return OneTimeInvoiceListResource::collection($collection);
    }

    /**
     * Zobrazenie jednej faktúry.
     */
    #[UrlParam('invoice', 'ID of the invoice', example: 1)]
    public function show(Invoice $invoice): OneTimeInvoiceResource
    {
        if (!$this->isUserSuperadmin()) {
            abort(403, 'Unauthorized');
        }

        $invoice->load('items');

        return new OneTimeInvoiceResource($invoice);
    }

    /**
     * Získa posledné číslo faktúry pre firmu.
     */
    #[UrlParam('company_id', 'ID of the company', example: 1)]
    public function getLastInvoiceNumber(int $company_id, int $billing_year): JsonResponse
    {
        if (!$this->isUserSuperadmin()) {
            abort(403, 'Unauthorized');
        }

        $lastNumber = $this->invoiceService->getLastInvoiceNumber($company_id, $billing_year);
        return response()->json(['last_invoice_number' => $lastNumber ?? 'Žiadna faktúra']);
    }

    /**
     * Vytvorenie novej faktúry.
     */
    public function store(StoreRequest $request): JsonResponse
    {
        if (!$this->isUserSuperadmin()) {
            abort(403, 'Unauthorized');
        }

        $data = $request->validated();
        $invoice = $this->invoiceService->createInvoice($data);
        return response()->json(['id' => $invoice->id], 201);
    }

    /**
     * Aktualizácia existujúcej faktúry.
     */
    #[UrlParam('invoice', 'ID of the invoice to update', example: 1)]
    public function update(UpdateRequest $request, Invoice $invoice): JsonResponse
    {
        if (!$this->isUserSuperadmin()) {
            abort(403, 'Unauthorized');
        }

        $data = $request->validated();

        // ✅ Použij service pre biznis logiku
        $updatedInvoice = $this->invoiceService->updateInvoice($invoice, $data);

        return response()->json(['id' => $updatedInvoice->id], 200);
    }

    // /**
    //  *   Vytvorenie novej faktúry z mesačnej faktúry.
    //  */
    // public function createOneTimeFromMonthly(CreateOneTimeFromMonthly $request): Response
    // {
    //     $this->authorize('create', Invoice::class);
    //     $data = $request->validated();

    //     $this->invoiceService->createOneTimeFromMonthly($data);

    //     // Asynchrónne generovanie QR kódov pre novovytvorené faktúry
    //     $newInvoiceIds = OneTimeInvoice::where('billing_year', $data['billing_year'])
    //         ->where('billing_month', $data['billing_month'])
    //         ->whereNull('qr_code')
    //         ->pluck('id')
    //         ->toArray();

    //     if (!empty($newInvoiceIds)) {
    //         // Pre bulk operácie použijeme asynchrónne generovanie
    //         GenerateQrCodeBulkJob::dispatch($newInvoiceIds);
    //     }

    //     return response()->noContent();
    // }

    /**
     * Vymazanie faktúry.
     */
    #[UrlParam('invoice', 'ID of the invoice to delete', example: 1)]
    public function delete(Invoice $invoice): JsonResponse
    {
        if (!$this->isUserSuperadmin()) {
            abort(403, 'Unauthorized');
        }

        $this->invoiceRepo->delete($invoice);
        return response()->json(null, 204);
    }
}
