<?php

namespace App\Observers;

use App\Jobs\GenerateQrCodeJob;
use App\Models\Invoice;
use App\Models\OneTimeInvoice;
use Illuminate\Support\Facades\Log;

class InvoiceObserver
{
    public function created(Invoice $invoice): void
    {
        GenerateQrCodeJob::dispatch($invoice);
        // ✅ Cleanup odstránený - robí sa v Service
    }

    public function updated(Invoice $invoice): void
    {
        GenerateQrCodeJob::dispatch($invoice);
    }
}
