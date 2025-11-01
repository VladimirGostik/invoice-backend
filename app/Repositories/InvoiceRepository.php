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
use Spatie\QueryBuilder\AllowedFilter;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function searchOneTime(array $filter): Collection|LengthAwarePaginator|array
    {
        $query = QueryBuilder::for(OneTimeInvoice::class)
            ->with(['items', 'company', 'residentialCompany', 'street'])
            ->allowedFilters([
                // ✅ Textové polia - partial (case-insensitive LIKE)
                AllowedFilter::partial('invoice_number'),
                AllowedFilter::partial('invoice_name'),
                AllowedFilter::partial('company_name'),
                AllowedFilter::partial('residential_company_name'),
                AllowedFilter::partial('status'),

                // ✅ Číselné/ID polia - exact match
                AllowedFilter::exact('company_id'),
                AllowedFilter::exact('residential_company_id'),
                AllowedFilter::exact('street_id'),

                // ✅ Dátumové polia - exact match
                AllowedFilter::exact('issued_at'),
                AllowedFilter::exact('due_at'),

                // ✅ Číselné polia - exact match
                AllowedFilter::exact('total'),
            ])
            ->allowedSorts([
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
            ->with(['items', 'company', 'residentialCompany', 'street'])
            ->allowedFilters([
                // ✅ Textové polia - partial (case-insensitive LIKE)
                AllowedFilter::partial('invoice_name'),
                AllowedFilter::partial('company_name'),
                AllowedFilter::partial('residential_company_name'),

                // ✅ Číselné/ID polia - exact match
                AllowedFilter::exact('company_id'),
                AllowedFilter::exact('residential_company_id'),
                AllowedFilter::exact('street_id'),

                // ✅ Číselné polia - exact match
                AllowedFilter::exact('total'),
            ])
            ->allowedSorts([
                'invoice_name',
                'company_id',
                'company_name',
                'residential_company_id',
                'residential_company_name',
                'street_id',
                'total',
                'created_at',
                'updated_at',
            ]);

        // Get pagination...
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

            return $invoice->fresh('items');
        });
    }

    public function update(Invoice $invoice, array $data): Invoice
    {
        $items = $data['items'] ?? [];
        unset($data['items']);

        return DB::transaction(function () use ($invoice, $data, $items) {
            $invoice->update($data);

            // ✅ Odstráň staré items a pridaj nové
            if (!empty($items)) {
                $invoice->items()->delete();

                foreach ($items as $item) {
                    $invoice->items()->create($item);
                }
            }

            return $invoice->fresh('items');
        });
    }

    public function delete(Invoice $invoice): void
    {
        DB::transaction(function () use ($invoice) {
            // Najprv vymaž items
            $invoice->items()->delete();
            // Potom vymaž faktúru
            $invoice->delete();
        });
    }
}
