<?php

namespace App\Repositories\Interfaces;

use App\Models\Invoice;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface InvoiceRepositoryInterface
{
    public function searchOneTime(array $filter): Collection|LengthAwarePaginator|array;
    public function searchMonthly(array $filter): Collection|LengthAwarePaginator|array;
    public function createOneTime(array $data): Invoice;
    public function createMonthly(array $data): Invoice;
    public function update(Invoice $invoice, array $data): Invoice;
    public function delete(Invoice $invoice): void;
}