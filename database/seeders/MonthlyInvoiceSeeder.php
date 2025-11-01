<?php

namespace Database\Seeders;

use App\Enums\CompanyTypeEnum;
use App\Models\Company;
use App\Models\MonthlyInvoice;
use App\Models\InvoiceItem;
use App\Models\Street;
use Illuminate\Database\Seeder;

class MonthlyInvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📄 Creating monthly invoices...');

        // Získaj hlavnú spoločnosť (vydavateľ faktúr)
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

        // Získaj všetky ulice
        $allStreets = Street::all();

        if ($allStreets->isEmpty()) {
            $this->command->error('❌ No streets found. Run StreetSeeder first!');
            return;
        }

        $totalInvoices = 0;
        $totalItems = 0;

        // Pre každú bytovú spoločnosť vytvor faktúry
        foreach ($residentialCompanies as $residentialCompany) {
            // Získaj ulice pre túto bytovú spoločnosť
            $companyStreets = $allStreets->where('company_id', $residentialCompany->id);

            if ($companyStreets->isEmpty()) {
                $this->command->warn("⚠️  No streets for {$residentialCompany->company_name}, skipping...");
                continue;
            }

            // Pre každú ulicu vytvor 1 DRAFT faktúru
            foreach ($companyStreets as $street) {
                $invoice = $this->createInvoice(
                    mainCompany: $mainCompany,
                    residentialCompany: $residentialCompany,
                    street: $street,
                    name: "Mesačná faktúra - {$street->street_name}",
                    isComplex: false
                );

                $totalInvoices++;
                $totalItems += $invoice->items()->count();

                $this->command->info("✅ Created DRAFT invoice: {$invoice->invoice_name}");
            }

            // Pre prvé 2 ulice vytvor aj komplexnú faktúru
            foreach ($companyStreets->take(2) as $street) {
                $invoice = $this->createInvoice(
                    mainCompany: $mainCompany,
                    residentialCompany: $residentialCompany,
                    street: $street,
                    name: "Komplexná faktúra - {$street->street_name}",
                    isComplex: true
                );

                $totalInvoices++;
                $totalItems += $invoice->items()->count();

                $this->command->info("✅ Created COMPLEX invoice: {$invoice->invoice_name}");
            }
        }

        $this->command->info("🎉 Successfully created:");
        $this->command->info("   - {$totalInvoices} monthly invoices");
        $this->command->info("   - {$totalItems} invoice items");
    }

    /**
     * Helper metóda na vytvorenie faktúry s položkami
     */
    private function createInvoice(
        Company $mainCompany,
        Company $residentialCompany,
        Street $street,
        string $name,
        bool $isComplex
    ): MonthlyInvoice {
        // Vytvor faktúru
        $invoice = MonthlyInvoice::factory()->create([
            'company_id' => $mainCompany->id,
            'residential_company_id' => $residentialCompany->id,
            'street_id' => $street->id,
            'invoice_name' => $name,
            'is_complex_billing' => $isComplex,
            'additional_info_1' => $isComplex ? 'Fond opráv: 15%' : null,
            'additional_info_2' => $isComplex ? 'Rezerva: 5%' : null,

            // Snapshot údajov z residential company
            'company_name' => $residentialCompany->company_name,
            'company_city' => $residentialCompany->company_city,
            'company_state' => $residentialCompany->company_state,
            'company_address' => $residentialCompany->company_address,
            'company_zip' => $residentialCompany->company_zip,
            'company_ico' => $residentialCompany->company_ico,
            'company_dic' => $residentialCompany->company_dic,
            'company_ic_dph' => $residentialCompany->company_ic_dph,
        ]);

        // Vytvor položky
        $itemsCount = $isComplex ? rand(5, 10) : rand(3, 7);
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

        // ✅ Použite MonthlyInvoice::find() namiesto fresh()
        return MonthlyInvoice::with('items')->find($invoice->id);
    }
}
