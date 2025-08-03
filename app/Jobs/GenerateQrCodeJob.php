<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Services\QrCodeGenerationService;
use Illuminate\Bus\Queueable;
use Illuminate\Container\Attributes\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateQrCodeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Invoice $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function handle(QrCodeGenerationService $qrCodeGenerationService): void
    {

        $qrCodeBase64 = $qrCodeGenerationService->generate(
            iban: $this->invoice->company_bank_account,
            amount: $this->invoice->total,
            variableSymbol: $this->invoice->variable_symbol,
            dueDate: $this->invoice->due_at,
            payeeName: $this->invoice->residential_company_name,
        );

        $this->invoice->qr_code = $qrCodeBase64;
        $this->invoice->save();
    }
}