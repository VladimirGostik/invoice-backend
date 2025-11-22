<?php
// filepath: /Users/vladimirgostik/osobne_projekty/invoice-backend/app/Repositories/MonthlyInvoiceRepository.php

namespace App\Repositories;

use App\Models\MonthlyInvoice;
use App\Repositories\Interfaces\MonthlyInvoiceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class MonthlyInvoiceRepository implements MonthlyInvoiceRepositoryInterface
{
    public function search(array $filter): Collection|LengthAwarePaginator|array
    {
        $query = QueryBuilder::for(MonthlyInvoice::class)
            ->with([
                'company:id,company_name',
                'street:id,street_name,company_id',
            ])
            ->allowedFilters([
                AllowedFilter::partial('invoice_name'),
                AllowedFilter::partial('company_name'),
                AllowedFilter::exact('company_id'),
                AllowedFilter::exact('residential_company_id'),
                AllowedFilter::exact('street_id'),
                AllowedFilter::exact('status'),
            ])
            ->allowedSorts([
                'invoice_name',
                'company_id',
                'residential_company_id',
                'street_id',
                'billing_year',
                'billing_month',
                'total',
                'created_at',
            ])
            ->defaultSort('-created_at');

        $perPage = $filter['per_page'] ?? null;

        // ✅ Handle pagination
        if ($perPage === null || $perPage === '' || $perPage === 'all') {
            return $query->get();
        }

        $paginate = (int)$perPage;
        return $paginate > 0 ? $query->paginate($paginate) : $query->get();
    }

    public function create(array $data): MonthlyInvoice
    {
        $items = $data['items'] ?? [];
        unset($data['items']);

        return DB::transaction(function () use ($data, $items) {
            $invoice = MonthlyInvoice::create($data);

            foreach ($items as $item) {
                $invoice->items()->create($item);
            }

            // ✅ Eager load relationships pre response
            return $invoice->fresh(['items', 'company', 'residentialCompany', 'street']);
        });
    }

    public function update(MonthlyInvoice $invoice, array $data): MonthlyInvoice
    {
        $items = $data['items'] ?? [];
        unset($data['items']);

        return DB::transaction(function () use ($invoice, $data, $items) {
            $invoice->update($data);

            if (!empty($items)) {
                $invoice->items()->delete();
                foreach ($items as $item) {
                    $invoice->items()->create($item);
                }
            }

            // ✅ Eager load relationships pre response
            return $invoice->fresh(['items', 'company', 'residentialCompany', 'street']);
        });
    }

    public function delete(MonthlyInvoice $invoice): void
    {
        DB::transaction(function () use ($invoice) {
            $invoice->items()->delete();
            $invoice->delete();
        });
    }
}
