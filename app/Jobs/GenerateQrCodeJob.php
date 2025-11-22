<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Services\QrCodeGenerationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateQrCodeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Invoice $invoice;

    public $timeout = 30;
    public $tries = 3;
    public $backoff = [10, 30, 60]; // Exponential backoff

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function handle(QrCodeGenerationService $qrCodeGenerationService): void
    {
        try {
            Log::info('Starting QR code generation for invoice: ' . $this->invoice->id);

            // Eager load relationships
            $this->invoice->load(['company', 'residentialCompany']);

            // Validácia - údaje z main company (issuer)
            $bankAccount = $this->invoice->company->company_bank_account;

            if (empty($bankAccount)) {
                Log::warning('Missing bank account for invoice: ' . $this->invoice->id);
                return;
            }

            if (!$this->invoice->total || $this->invoice->total <= 0) {
                Log::warning('Invalid total amount for invoice: ' . $this->invoice->id);
                return;
            }

            if (empty($this->invoice->variable_symbol)) {
                Log::warning('Missing variable symbol for invoice: ' . $this->invoice->id);
                return;
            }

            // Payee name - z residential company snapshot alebo relationship
            $payeeName = $this->invoice->residential_company_name
                      ?? $this->invoice->residentialCompany?->company_name;

            if (empty($payeeName)) {
                Log::warning('Missing payee name for invoice: ' . $this->invoice->id);
                return;
            }

            // Vygeneruj QR kód
            $qrCodeBase64 = $qrCodeGenerationService->generate(
                iban: $bankAccount,
                amount: $this->invoice->total,
                variableSymbol: $this->invoice->variable_symbol,
                dueDate: $this->invoice->due_at,
                payeeName: $payeeName,
            );

            // Použijeme updateQuietly pre zabránenie triggerovania Observer-a
            $this->invoice->updateQuietly(['qr_code' => $qrCodeBase64]);

            Log::info('QR code generated successfully for invoice: ' . $this->invoice->id);

        } catch (\Exception $e) {
            Log::error('Failed to generate QR code for invoice: ' . $this->invoice->id . '. Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            // Re-throw exception pre retry mechanism
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('QR code generation job failed permanently for invoice: ' . $this->invoice->id . '. Error: ' . $exception->getMessage());
    }
}
