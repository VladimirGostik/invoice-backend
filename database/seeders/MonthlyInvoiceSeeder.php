<?php

namespace Database\Seeders;

use App\Enums\CompanyTypeEnum;
use App\Models\Company;
use App\Models\MonthlyInvoice;
use App\Models\MonthlyInvoiceItem;
use Illuminate\Database\Seeder;

class MonthlyInvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📄 Creating monthly invoices...');

        // ✅ Získaj main companies
        $mainCompanies = Company::where('company_type', CompanyTypeEnum::MAIN->value)->get();

        // ✅ Získaj residential companies
        $residentialCompanies = Company::where('company_type', CompanyTypeEnum::RESIDENTIAL->value)->get();

        if ($mainCompanies->isEmpty() || $residentialCompanies->isEmpty()) {
            $this->command->error('❌ Missing companies!');
            return;
        }

        $totalInvoices = 0;

        // ✅ Pre každú main company vytvor 5-10 faktúr
        foreach ($mainCompanies as $mainCompany) {

            // Vytvor 5-10 náhodných faktúr
            $count = rand(5, 10);

            for ($i = 1; $i <= $count; $i++) {
                // Náhodná residential company
                $resCompany = $residentialCompanies->random();

                // Náhodná ulica (alebo null)
                $street = $resCompany->streets()->inRandomOrder()->first();

                // Vytvor faktúru
                $invoice = MonthlyInvoice::factory()->create([
                    'company_id' => $mainCompany->id,
                    'residential_company_id' => $resCompany->id,
                    'street_id' => $street?->id,
                ]);

                // Vytvor 3-7 položiek
                MonthlyInvoiceItem::factory()
                    ->count(rand(3, 7))
                    ->create(['monthly_invoice_id' => $invoice->id]);

                // Prepočítaj total
                $subtotal = $invoice->items->sum('line_total');
                $invoice->update([
                    'subtotal' => $subtotal,
                    'tax' => round($subtotal * 0.20, 2),
                    'total' => round($subtotal * 1.20, 2),
                ]);

                $totalInvoices++;
            }
        }

        $this->command->info("✅ Created {$totalInvoices} monthly invoices");
    }
}
