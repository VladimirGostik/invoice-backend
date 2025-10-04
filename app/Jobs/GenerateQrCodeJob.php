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

            // Validácia potrebných dát
            if (empty($this->invoice->company_bank_account)) {
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

            if (empty($this->invoice->residential_company_name)) {
                Log::warning('Missing residential company name for invoice: ' . $this->invoice->id);
                return;
            }

            $qrCodeBase64 = $qrCodeGenerationService->generate(
                iban: $this->invoice->company_bank_account,
                amount: $this->invoice->total,
                variableSymbol: $this->invoice->variable_symbol,
                dueDate: $this->invoice->due_at,
                payeeName: $this->invoice->residential_company_name,
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
