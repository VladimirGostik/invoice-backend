<?php

namespace App\Repositories;

use App\Models\Invoice;
use App\Models\OneTimeInvoice;
use App\Models\MonthlyInvoice;
use App\Repositories\Interfaces\InvoiceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function searchOneTime(array $filter): Collection|LengthAwarePaginator|array
    {
        //$invoices = OneTimeInvoice::collection();

        $query = QueryBuilder::for(OneTimeInvoice::class)
            ->allowedFilters([
                'invoice_number',
                'company_id.company_name',
                'residential_company_id.company_name',
                'street_id.street_name',
                'status',
                'issued_at',
                'due_at',
                'total',
            ]);
            
        // Get pagination
        $paginate = (int)($filter['per_page'] ?? config('system.paginate'));

        return $paginate ?
            $query->paginate($paginate) :
            $query->get();
    }

    public function searchMonthly(array $filter): Collection|LengthAwarePaginator|array
    {
        $query = QueryBuilder::for(MonthlyInvoice::class)
            ->allowedFilters([
                'invoice_name',
                'company_id.company_name',
                'residential_company_id.company_name',
                'street_id.street_name',
                'issued_at',
                'due_at',
                'total',
            ]);
            
        // Get pagination
        $paginate = (int)($filter['per_page'] ?? config('system.paginate'));

        return $paginate ?
            $query->paginate($paginate) :
            $query->get();
    }

    public function createOneTime(array $data): Invoice
    {
        return $this->create(new OneTimeInvoice(), $data);
    }

    public function createMonthly(array $data): Invoice
    {
        return $this->create(new MonthlyInvoice(), $data);
    }

    private function create(Invoice $invoice, array $data): Invoice
    {
        $items = $data['items'] ?? [];
        unset($data['items']);

        $invoice->fill($data);
        $invoice->save();

        foreach ($items as $item) {
            $invoice->items()->create($item);
        }

        return $invoice;
    }

    public function update(Invoice $invoice, array $data): Invoice
    {
        $invoice->update($data);
        return $invoice;
    }

    public function delete(Invoice $invoice): void
    {
        $invoice->delete();
    }
}