<?php
// filepath: /Users/vladimirgostik/osobne_projekty/invoice-backend/app/Repositories/Interfaces/MonthlyInvoiceRepositoryInterface.php

namespace App\Repositories\Interfaces;

use App\Models\MonthlyInvoice;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface MonthlyInvoiceRepositoryInterface
{
    public function search(array $filter): Collection|LengthAwarePaginator|array;
    public function create(array $data): MonthlyInvoice;
    public function update(MonthlyInvoice $invoice, array $data): MonthlyInvoice;
    public function delete(MonthlyInvoice $invoice): void;
}
