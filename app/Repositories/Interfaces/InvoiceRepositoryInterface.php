<?php

namespace App\Repositories\Interfaces;

use App\Models\Invoice;
use App\Models\OneTimeInvoice;
use App\Models\MonthlyInvoice;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface InvoiceRepositoryInterface
{
    public function searchOneTime(array $filter): Collection|LengthAwarePaginator|array;
    public function searchMonthly(array $filter): Collection|LengthAwarePaginator|array;
    public function createOneTime(array $data): OneTimeInvoice;
    public function updateOneTime(OneTimeInvoice $invoice, array $data): OneTimeInvoice;
    public function createMonthly(array $data): MonthlyInvoice;
    public function updateMonthly(MonthlyInvoice $invoice, array $data): MonthlyInvoice;
    public function delete(Invoice $invoice): void;
}