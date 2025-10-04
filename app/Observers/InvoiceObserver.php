<?php

namespace App\Observers;

use App\Jobs\GenerateQrCodeJob;
use App\Models\OneTimeInvoice;
use Illuminate\Support\Facades\Log;

class InvoiceObserver
{
    public function created(OneTimeInvoice $invoice): void
    {
        // Generuj QR kód len ak máme všetky potrebné dáta
        if ($this->hasRequiredDataForQr($invoice)) {
            Log::info('Dispatching QR code generation job for invoice: ' . $invoice->id);
            // OneTimeInvoice extends Invoice, takže môžeme použiť priamo $invoice
            GenerateQrCodeJob::dispatch($invoice);
        } else {
            Log::info('Skipping QR code generation for invoice: ' . $invoice->id . ' - missing required data');
        }
    }

    public function updated(OneTimeInvoice $invoice): void
    {
        // Generuj QR kód len ak sa zmenili relevantné polia alebo QR kód neexistuje
        if ($this->shouldRegenerateQr($invoice)) {
            Log::info('Dispatching QR code generation job for updated invoice: ' . $invoice->id);
            GenerateQrCodeJob::dispatch($invoice);
        }
    }

    private function hasRequiredDataForQr(OneTimeInvoice $invoice): bool
    {
        return !empty($invoice->company_bank_account) &&
               $invoice->total > 0 &&
               !empty($invoice->variable_symbol) &&
               !empty($invoice->residential_company_name);
    }

    private function shouldRegenerateQr(OneTimeInvoice $invoice): bool
    {
        // Relevantné polia pre QR kód
        $relevantFields = ['company_bank_account', 'total', 'variable_symbol', 'due_at', 'residential_company_name'];

        $hasRelevantChanges = collect($relevantFields)->some(function ($field) use ($invoice) {
            return $invoice->isDirty($field);
        });

        return ($hasRelevantChanges || empty($invoice->qr_code)) && $this->hasRequiredDataForQr($invoice);
    }
}
