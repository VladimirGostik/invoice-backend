<?php

namespace Database\Seeders;

use App\Enums\CompanyTypeEnum;
use App\Enums\InvoiceStatusEnum;
use App\Models\Company;
use App\Models\OneTimeInvoice;
use App\Models\InvoiceItem;
use App\Models\Street;
use Illuminate\Database\Seeder;

class OneTimeInvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📄 Creating one-time invoices...');

        // Získaj hlavnú spoločnosť
        $mainCompany = Company::where('company_type', CompanyTypeEnum::MAIN->value)->first();

        if (!$mainCompany) {
            $this->command->error('❌ No main company found. Run CompanySeeder first!');
            return;
        }

        // Získaj všetky bytové spoločnosti
        $residentialCompanies = Company::where('company_type', CompanyTypeEnum::RESIDENTIAL->value)->get();

        if ($residentialCompanies->isEmpty()) {
            $this->command->error('❌ No residential companies found. Run CompanySeeder first!');
            return;
        }

        $totalInvoices = 0;
        $totalItems = 0;

        // Pre každú bytovú spoločnosť vytvor 2-3 jednorazové faktúry
        foreach ($residentialCompanies as $residentialCompany) {
            $invoicesCount = rand(2, 3);

            for ($i = 0; $i < $invoicesCount; $i++) {
                $statuses = [
                    InvoiceStatusEnum::DRAFT,
                    InvoiceStatusEnum::ISSUED,
                    InvoiceStatusEnum::PAID,
                ];

                $status = $statuses[array_rand($statuses)];

                // Vytvor faktúru
                $invoice = OneTimeInvoice::factory()->create([
                    'company_id' => $mainCompany->id,
                    'residential_company_id' => $residentialCompany->id,
                    'street_id' => null, // One-time faktúry nemusia mať ulicu
                    'invoice_name' => "Jednorazová faktúra #{$i} - {$residentialCompany->company_name}",
                    'status' => $status->value,

                    // Snapshot údajov
                    'company_name' => $residentialCompany->company_name,
                    'company_city' => $residentialCompany->company_city,
                    'company_state' => $residentialCompany->company_state,
                    'company_address' => $residentialCompany->company_address,
                    'company_zip' => $residentialCompany->company_zip,
                    'company_ico' => $residentialCompany->company_ico,
                    'company_dic' => $residentialCompany->company_dic,
                    'company_ic_dph' => $residentialCompany->company_ic_dph,

                    // Dátumy podľa statusu
                    'issued_at' => $status !== InvoiceStatusEnum::DRAFT ? now()->subDays(rand(1, 30)) : null,
                    'due_at' => $status !== InvoiceStatusEnum::DRAFT ? now()->addDays(rand(14, 30)) : null,
                    'payment_date' => $status === InvoiceStatusEnum::PAID ? now()->subDays(rand(1, 10)) : null,
                ]);

                // Vytvor položky
                $itemsCount = rand(1, 5);
                $items = InvoiceItem::factory()->count($itemsCount)->create([
                    'invoice_id' => $invoice->id,
                ]);

                // Prepočítaj totals
                $subtotal = $items->sum('line_total');
                $tax = round($subtotal * 0.20, 2);
                $total = $subtotal + $tax;

                $invoice->update([
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'total' => $total,
                ]);

                $totalInvoices++;
                $totalItems += $itemsCount;

                $statusLabel = match($status) {
                    InvoiceStatusEnum::DRAFT => 'DRAFT',
                    InvoiceStatusEnum::ISSUED => 'ISSUED',
                    InvoiceStatusEnum::PAID => 'PAID',
                    default => 'UNKNOWN',
                };

                $this->command->info("✅ Created {$statusLabel} invoice: {$invoice->invoice_name}");
            }
        }

        $this->command->info("🎉 Successfully created:");
        $this->command->info("   - {$totalInvoices} one-time invoices");
        $this->command->info("   - {$totalItems} invoice items");
    }
}
