<?php

namespace App\Observers;

use App\Jobs\GenerateQrCodeJob;
use App\Models\Invoice;
use App\Models\OneTimeInvoice;
use App\Enums\InvoiceTypeEnum;
use Illuminate\Support\Facades\Log;

class InvoiceObserver
{
    public function created(Invoice $invoice): void
    {
        // ✅ Generuj QR kód LEN pre jednorazové faktúry
        if ($invoice->type !== InvoiceTypeEnum::ONE_TIME->value) {
            return;
        }

        // Generuj QR kód len ak máme všetky potrebné dáta
        if ($this->hasRequiredDataForQr($invoice)) {
            Log::info('Dispatching QR code generation job for invoice: ' . $invoice->id);
            GenerateQrCodeJob::dispatch($invoice);
        } else {
            Log::info('Skipping QR code generation for invoice: ' . $invoice->id . ' - missing required data');
        }

        // Cleanup complex billing info
        if ($invoice->is_complex_billing === false) {
            $invoice->additional_info_1 = null;
            $invoice->additional_info_2 = null;
            $invoice->saveQuietly(); // ✅ Použite saveQuietly() aby sa nespustil observer znova
        }
    }

    public function updated(Invoice $invoice): void
    {
        // ✅ Regeneruj QR kód LEN pre jednorazové faktúry
        if ($invoice->type !== InvoiceTypeEnum::ONE_TIME->value) {
            return;
        }

        // Generuj QR kód len ak sa zmenili relevantné polia alebo QR kód neexistuje
        if ($this->shouldRegenerateQr($invoice)) {
            Log::info('Dispatching QR code generation job for updated invoice: ' . $invoice->id);
            GenerateQrCodeJob::dispatch($invoice);
        }

        // Cleanup complex billing info
        if ($invoice->isDirty('is_complex_billing') && $invoice->is_complex_billing === false) {
            $invoice->additional_info_1 = null;
            $invoice->additional_info_2 = null;
            $invoice->saveQuietly(); // ✅ Použite saveQuietly()
        }
    }

    private function hasRequiredDataForQr(Invoice $invoice): bool
    {
        return !empty($invoice->company_bank_account) &&
               $invoice->total > 0 &&
               !empty($invoice->variable_symbol) &&
               !empty($invoice->company_name);
    }

    private function shouldRegenerateQr(Invoice $invoice): bool
    {
        // Relevantné polia pre QR kód
        $relevantFields = ['company_bank_account', 'total', 'variable_symbol', 'due_at', 'company_name'];

        $hasRelevantChanges = collect($relevantFields)->some(function ($field) use ($invoice) {
            return $invoice->isDirty($field);
        });

        return ($hasRelevantChanges || empty($invoice->qr_code)) && $this->hasRequiredDataForQr($invoice);
    }
}
