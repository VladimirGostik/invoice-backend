<?php

namespace App\Services;

use App\Repositories\Interfaces\InvoiceRepositoryInterface;
use App\Models\Invoice;
use App\Models\Company;
use App\Models\MonthlyInvoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceService
{
    protected $invoiceRepo;

    public function __construct(InvoiceRepositoryInterface $invoiceRepo)
    {
        $this->invoiceRepo = $invoiceRepo;
    }

    /**
     * Thread-safe generovanie čísla faktúry
     */
    public function generateInvoiceNumber(int $company_id, int $billing_year): string
    {
        return DB::transaction(function () use ($company_id, $billing_year) {
            $lastInvoice = Invoice::where('company_id', $company_id)
                ->where('billing_year', $billing_year)
                ->whereNotNull('invoice_number')
                ->lockForUpdate()
                ->orderBy('invoice_number', 'desc')
                ->first();

            $currentYear = (string)$billing_year;
            $nextSequence = 1;

            if ($lastInvoice && $lastInvoice->invoice_number) {
                // Extrahujeme číselnú časť (posledných 5 znakov)
                $yearPrefix = substr($lastInvoice->invoice_number, 0, 4);

                if ($yearPrefix === $currentYear) {
                    $sequencePart = substr($lastInvoice->invoice_number, 4);
                    $nextSequence = (int)$sequencePart + 1;
                }
            }

            $newInvoiceNumber = sprintf('%s%03d', $currentYear, $nextSequence);

            // Double-check na unikátnosť
            while (Invoice::where('invoice_number', $newInvoiceNumber)->exists()) {
                $nextSequence++;
                $newInvoiceNumber = sprintf('%s%03d', $currentYear, $nextSequence);
            }

            return $newInvoiceNumber;
        });
    }

    public function getLastInvoiceNumber(int $company_id, int $billing_year): ?string
    {
        $lastInvoice = Invoice::where('company_id', $company_id)
            ->where('billing_year', $billing_year)
            ->orderBy('invoice_number', 'desc')
            ->first();

        return $lastInvoice ? $lastInvoice->invoice_number : null;
    }

    public function generateVariableSymbol(string $invoiceNumber): string
    {
        // Extrahujeme posledných 5 číslic z čísla faktúry
        $numberPart = substr($invoiceNumber, -3);
        // Kombinujeme s rokom (max. 10 číslic pre variabilný symbol)
        return sprintf('%s%s', date('Y'), $numberPart); // ex. "2025001"
    }

    public function createInvoice($data)
    {
        // ✅ Eager load company s customization v JEDNOM query
        $company = Company::with('companyCustomization')
            ->findOrFail($data['company_id']);

        $companyCustomization = $company->companyCustomization;

        $data['invoice_number'] = $this->generateInvoiceNumber(
            $data['company_id'],
            $data['billing_year']
        );

        $data['variable_symbol'] = $this->generateVariableSymbol(
            $data['invoice_number']
        );

        // ✅ Vyčisti complex billing PRED vytvorením
        if (empty($data['is_complex_billing'])) {
            $data['additional_info_1'] = null;
            $data['additional_info_2'] = null;
        }

        // ✅ Merge snapshot
        $data = array_merge(
            $data,
            $companyCustomization ? $companyCustomization->snapshot() : [],
        );

        return $this->invoiceRepo->create($data);
    }

    /**
     * Optimalizovaná metóda pre bulk vytváranie faktúr z mesačných
     */
    public function createOneTimeFromMonthly(array $data): void
    {
        DB::transaction(function () use ($data) {
            $monthlyInvoices = MonthlyInvoice::with(['items'])
                ->whereIn('id', $data['monthly_invoice_ids'])
                ->orderBy('company_id')
                ->orderBy('residential_company_id')
                ->get();

            $companyIds = $monthlyInvoices->pluck('company_id')->unique();
            $companies = Company::with('companyCustomization')
                ->whereIn('id', $companyIds)
                ->get()
                ->keyBy('id');

            // Pre bulk operácie vypneme Observer
            $invoicesData = [];
            $invoiceNumbers = [];

            foreach ($companyIds as $companyId) {
                $companyInvoices = $monthlyInvoices->where('company_id', $companyId);
                $company = $companies[$companyId];

                // Získame aktuálny počítač pre firmu
                $lastInvoice = Invoice::where('company_id', $companyId)
                    ->where('billing_year', $data['billing_year'])
                    ->whereNotNull('invoice_number')
                    ->orderBy('invoice_number', 'desc')
                    ->lockForUpdate()
                    ->first();

                $currentYear = (string)$data['billing_year'];
                $nextSequence = 1;

                if ($lastInvoice && $lastInvoice->invoice_number) {
                    $yearPrefix = substr($lastInvoice->invoice_number, 0, 4);
                    if ($yearPrefix === $currentYear) {
                        $sequencePart = substr($lastInvoice->invoice_number, 4);
                        $nextSequence = (int)$sequencePart + 1;
                    }
                }

                foreach ($companyInvoices as $monthlyInvoice) {
                    $invoiceNumber = sprintf('%s%05d', $currentYear, $nextSequence);
                    $invoiceNumbers[] = $invoiceNumber;

                    $invoiceData = $monthlyInvoice->snapshot();
                    $invoiceData['invoice_number'] = $invoiceNumber;
                    $invoiceData['variable_symbol'] = $this->generateVariableSymbol($invoiceNumber);
                    $invoiceData['issued_at'] = $data['issued_at'];
                    $invoiceData['due_at'] = $data['due_at'];
                    $invoiceData['billing_year'] = $data['billing_year'];
                    $invoiceData['billing_month'] = $data['billing_month'];
                    $invoiceData['company_customization_snapshot'] = $company->companyCustomization
                        ? $company->companyCustomization->snapshot()
                        : [];
                    $invoiceData['items'] = $monthlyInvoice->items->map(fn($item) => $item->toArray())->toArray();

                    $invoicesData[] = $invoiceData;
                    $nextSequence++;
                }
            }

            // Bulk vytvorenie bez triggerov
            foreach ($invoicesData as $invoiceData) {
                $this->invoiceRepo->create($invoiceData);
            }

            Log::info('Bulk created ' . count($invoicesData) . ' invoices from monthly invoices');
        });
    }

    /**
     * Bulk generovanie QR kódov pre existujúce faktúry
     */
    public function generateQrCodesForInvoices(array $invoiceIds): void
    {
        $qrService = app(QrCodeGenerationService::class);

        $invoices = Invoice::whereIn('id', $invoiceIds)
            ->whereNull('qr_code')
            ->get();

        foreach ($invoices as $invoice) {
            try {
                if (empty($invoice->company_bank_account) ||
                    !$invoice->total ||
                    empty($invoice->variable_symbol) ||
                    empty($invoice->residential_company_name)) {
                    continue;
                }

                $qrCodeBase64 = $qrService->generate(
                    iban: $invoice->company_bank_account,
                    amount: $invoice->total,
                    variableSymbol: $invoice->variable_symbol,
                    dueDate: $invoice->due_at,
                    payeeName: $invoice->residential_company_name,
                );

                $invoice->updateQuietly(['qr_code' => $qrCodeBase64]);

            } catch (\Exception $e) {
                Log::error('Failed to generate QR code for invoice: ' . $invoice->id . '. Error: ' . $e->getMessage());
            }
        }

        Log::info('Bulk generated QR codes for ' . $invoices->count() . ' invoices');
    }

    public function updateInvoice(Invoice $invoice, array $data): Invoice
    {
        // ✅ Vyčisti complex billing PRED update
        if (isset($data['is_complex_billing']) && $data['is_complex_billing'] === false) {
            $data['additional_info_1'] = null;
            $data['additional_info_2'] = null;
        }

        // ✅ Ak sa mení company, preload customization
        if (isset($data['company_id']) && $data['company_id'] !== $invoice->company_id) {
            $company = Company::with('companyCustomization')
                ->findOrFail($data['company_id']);

            $companyCustomization = $company->companyCustomization;

            if ($companyCustomization) {
                $data = array_merge($data, $companyCustomization->snapshot());
            }
        }

        return $this->invoiceRepo->update($invoice, $data);
    }
}
