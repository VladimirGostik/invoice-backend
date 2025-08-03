<?php

namespace App\Observers;

use App\Jobs\GenerateQrCodeJob;
use App\Models\OneTimeInvoice;

class InvoiceObserver
{
    public function created(OneTimeInvoice $invoice): void
    {
        GenerateQrCodeJob::dispatch($invoice);
    }
}