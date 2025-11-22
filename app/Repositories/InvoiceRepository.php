<?php

namespace App\Repositories;

use App\Models\Invoice;
use App\Repositories\Interfaces\InvoiceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function search(array $filter): Collection|LengthAwarePaginator|array
    {
        $query = QueryBuilder::for(Invoice::class)
            ->with(['items', 'company', 'residentialCompany', 'street'])
            ->allowedFilters([
                AllowedFilter::partial('invoice_number'),
                AllowedFilter::partial('invoice_name'),
                AllowedFilter::partial('company_name'),
                AllowedFilter::partial('status'),

                AllowedFilter::exact('company_id'),
                AllowedFilter::exact('residential_company_id'),
                AllowedFilter::exact('street_id'),

                AllowedFilter::exact('issued_at'),
                AllowedFilter::exact('due_at'),

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

    public function create(array $data): Invoice
    {
        $items = $data['items'] ?? [];
        unset($data['items']);

        return DB::transaction(function () use ($data, $items) {
            $invoice = Invoice::create($data);

            if (!empty($items)) {
                $invoiceItems = array_map(function($item) use ($invoice) {
                    return array_merge($item, [
                        'invoice_id' => $invoice->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }, $items);

                DB::table('invoice_items')->insert($invoiceItems);

                // ✅ Vytvor collection manuálne BEZ SELECT query
                $invoice->setRelation('items',
                    collect($invoiceItems)->map(fn($item) =>
                        new \App\Models\InvoiceItem($item)
                    )
                );
            }

            return $invoice;
        });
    }

    public function update(Invoice $invoice, array $data): Invoice
    {
        $items = $data['items'] ?? null;
        unset($data['items']);

        return DB::transaction(function () use ($invoice, $data, $items) {
            // ✅ Update invoice
            $invoice->update($data);

            // ✅ Ak sú items, synchronizuj ich
            if ($items !== null) {
                $this->syncItems($invoice, $items);
            }

            // ✅ Reload items po sync
            $invoice->load('items');

            return $invoice;
        });
    }

    // ✅ Helper na sync items (efektívnejšie ako delete all + insert)
    protected function syncItems(Invoice $invoice, array $items): void
    {
        $existingItemIds = [];

        foreach ($items as $itemData) {
            if (isset($itemData['id'])) {
                // Update existujúceho item
                $invoice->items()->where('id', $itemData['id'])->update([
                    'description' => $itemData['description'],
                    'unit' => $itemData['unit'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'line_total' => $itemData['line_total'],
                ]);
                $existingItemIds[] = $itemData['id'];
            } else {
                // Vytvor nový item
                $newItem = $invoice->items()->create($itemData);
                $existingItemIds[] = $newItem->id;
            }
        }

        // ✅ Vymaž items, ktoré už nie sú v requeste
        $invoice->items()->whereNotIn('id', $existingItemIds)->delete();
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
