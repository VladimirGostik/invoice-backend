<?php

namespace App\Repositories;

use App\Models\Invoice;
use App\Models\OneTimeInvoice;
use App\Models\MonthlyInvoice;
use App\Repositories\Interfaces\InvoiceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\DB;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function searchOneTime(array $filter): Collection|LengthAwarePaginator|array
    {
        $query = QueryBuilder::for(OneTimeInvoice::class)
            ->OneTime()
            ->with(['items'])
            ->allowedFilters([
                'invoice_number',
                'invoice_name',
                'company_id',
                'company_name',
                'residential_company_id',
                'residential_company_name',
                'street_id',
                'status',
                'issued_at',
                'due_at',
                'total',
            ])->allowedSorts([
                'invoice_number',
                'company_id',
                'residential_company_id',
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
            ->Monthly()
            ->with(['items'])
            ->allowedFilters([
                'invoice_name',
                'invoice_name',
                'company_id',
                'company_name',
                'residential_company_id',
                'residential_company_name',
                'street_id',
                'total',
            ])->allowedSorts([
                'invoice_name',
                'invoice_name',
                'company_id',
                'company_name',
                'residential_company_id',
                'residential_company_name',
                'street_id',
                'total',
            ]);
            
        // Get pagination
        $paginate = (int)($filter['per_page'] ?? config('system.paginate'));

        return $paginate ?
            $query->paginate($paginate) :
            $query->get();
    }

    public function createOneTime(array $data): OneTimeInvoice
    {
        return $this->create(new OneTimeInvoice(), $data);
    }

    public function updateOneTime(OneTimeInvoice $invoice, array $data): OneTimeInvoice
    {
        return $this->update($invoice, $data);
    }

    public function createMonthly(array $data): MonthlyInvoice
    {
        return $this->create(new MonthlyInvoice(), $data);
    }

    public function updateMonthly(MonthlyInvoice $invoice, array $data): MonthlyInvoice
    {
        return $this->update($invoice, $data);
    }

    private function create(Invoice $invoice, array $data): Invoice
    {
        $items = $data['items'] ?? [];
        unset($data['items']);

        return DB::transaction(function () use ($invoice, $data, $items) {
            $invoice->fill($data);
            $invoice->save();

            foreach ($items as $item) {
                $invoice->items()->create($item);
            }

            return $invoice;
        });
    }

    public function update(Invoice $invoice, array $data): Invoice
    {
        $items = $data['items'] ?? [];
        unset($data['items']);

        $invoice->update($data);

        foreach ($items as $item) {
            $invoice->items()->update($item);
        }

        return $invoice;
    }

    public function delete(Invoice $invoice): void
    {
        $invoice->delete();
    }
}