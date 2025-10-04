<?php

namespace App\Jobs;

use App\Services\InvoiceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateQrCodeBulkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $invoiceIds;

    public $timeout = 300; // 5 minút
    public $tries = 2;

    public function __construct(array $invoiceIds)
    {
        $this->invoiceIds = $invoiceIds;
    }

    public function handle(InvoiceService $invoiceService): void
    {
        Log::info('Starting bulk QR code generation for ' . count($this->invoiceIds) . ' invoices');

        try {
            $invoiceService->generateQrCodesForInvoices($this->invoiceIds);

            Log::info('Bulk QR code generation completed successfully');

        } catch (\Exception $e) {
            Log::error('Bulk QR code generation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Bulk QR code generation job failed permanently: ' . $exception->getMessage());
    }
}
