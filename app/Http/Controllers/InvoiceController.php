<?php

namespace App\Http\Controllers;

use App\Http\Requests\Invoice\StoreRequest;
use App\Http\Requests\Invoice\StoreMonthlyRequest;
use App\Http\Requests\Invoice\UpdateMonthlyRequest;
use App\Http\Requests\Invoice\CreateOneTimeFromMonthly;
use App\Http\Resources\OneTimeInvoiceResource;
use App\Http\Resources\MonthlyInvoiceResource;
use App\Jobs\GenerateQrCodeBulkJob as JobsGenerateQrCodeBulkJob;
use App\Models\Invoice;
use App\Models\OneTimeInvoice;
use App\Models\MonthlyInvoice;
use App\Repositories\Interfaces\InvoiceRepositoryInterface;
use App\Services\InvoiceService;
use GenerateQrCodeBulkJob;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
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
     * Zoznam jednorazovych faktúr.
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
    public function searchMonthly(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Invoice::class);
        $filters = $request->all();
        $collection = $this->invoiceRepo->searchMonthly($filters);
        return MonthlyInvoiceResource::collection($collection);
    }

    /**
     * Zobrazenie jednej mesacnej faktúry.
     */
    #[UrlParam('invoice', 'ID of the invoice', example: 1)]
    public function viewMonthly(Invoice $invoice): MonthlyInvoiceResource
    {
        $this->authorize('view', $invoice);
        return new MonthlyInvoiceResource($invoice);
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
        return response()->json(['id' => $invoice->id], 201);
    }

    /**
     * Aktualizácia existujúcej faktúry.
     */
    #[UrlParam('invoice', 'ID of the invoice to update', example: 1)]
    public function updateOneTime(StoreRequest $request, OneTimeInvoice $invoice): JsonResponse
    {
        $this->authorize('update',  Invoice::class);
        $data = $request->validated();
        $updatedInvoice = $this->invoiceRepo->updateOneTime($invoice, $data);
        return response()->json(['id' => $updatedInvoice->id], 200);
    }

    /**
     * Vytvorenie novej mesacnej faktúry.
     */
    public function storeMonthly(StoreMonthlyRequest $request): JsonResponse
    {
        $this->authorize('create', Invoice::class);
        $data = $request->validated();

        $invoice = $this->invoiceService->createMonthly($data);
        return response()->json(['id' => $invoice->id], 201);
    }

    /**
     * Aktualizácia existujúcej mesačnej faktúry.
     */
    public function updateMonthly(UpdateMonthlyRequest $request, MonthlyInvoice $invoice): JsonResponse
    {
        $this->authorize('update',  MonthlyInvoice::class);
        $data = $request->validated();
        $updatedInvoice = $this->invoiceRepo->updateMonthly($invoice, $data);
        return response()->json(['id' => $updatedInvoice->id], 200);
    }

    /**
     *   Vytvorenie novej faktúry z mesačnej faktúry.
     */
    public function createOneTimeFromMonthly(CreateOneTimeFromMonthly $request): Response
    {
        $this->authorize('create', Invoice::class);
        $data = $request->validated();

        $this->invoiceService->createOneTimeFromMonthly($data);

        // Asynchrónne generovanie QR kódov pre novovytvorené faktúry
        $newInvoiceIds = OneTimeInvoice::where('billing_year', $data['billing_year'])
            ->where('billing_month', $data['billing_month'])
            ->whereNull('qr_code')
            ->pluck('id')
            ->toArray();

        if (!empty($newInvoiceIds)) {
            // Pre bulk operácie použijeme asynchrónne generovanie
            JobsGenerateQrCodeBulkJob::dispatch($newInvoiceIds);
        }

        return response()->noContent();
    }

    /**
     * Manuálne generovanie QR kódov pre faktúry
     */
    public function generateQrCodes(Request $request): JsonResponse
    {
        $this->authorize('update', Invoice::class);

        $invoiceIds = $request->input('invoice_ids', []);

        if (empty($invoiceIds)) {
            return response()->json(['error' => 'No invoice IDs provided'], 400);
        }

        $this->invoiceService->generateQrCodesForInvoices($invoiceIds);

        return response()->json(['message' => 'QR codes generation completed']);
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
}
