<?php

namespace App\Repositories\Interfaces;

use App\Models\Invoice;
use App\Models\MonthlyInvoice;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface InvoiceRepositoryInterface
{
    public function search(array $filter): Collection|LengthAwarePaginator|array;
    public function create(array $data): Invoice;
    public function update(Invoice $invoice, array $data): Invoice;
    public function delete(Invoice $invoice): void;
}
