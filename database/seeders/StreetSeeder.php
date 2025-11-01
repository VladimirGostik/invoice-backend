<?php

namespace Database\Seeders;

use App\Enums\CompanyTypeEnum;
use App\Models\Company;
use App\Models\Street;
use Illuminate\Database\Seeder;

class StreetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏘️ Creating streets...');

        // Získaj všetky bytové spoločnosti
        $residentialCompanies = Company::where('company_type', CompanyTypeEnum::RESIDENTIAL->value)->get();

        if ($residentialCompanies->isEmpty()) {
            $this->command->error('❌ No residential companies found. Run CompanySeeder first!');
            return;
        }

        $totalStreets = 0;

        // Pre každú bytovú spoločnosť vytvor 2-4 ulice
        foreach ($residentialCompanies as $company) {
            $streetsCount = rand(2, 4);

            Street::factory()
                ->count($streetsCount)
                ->create([
                    'company_id' => $company->id,
                ]);

            $totalStreets += $streetsCount;

            $this->command->info("✅ Created {$streetsCount} streets for {$company->company_name}");
        }

        $this->command->info("🎉 Total streets created: {$totalStreets}");
    }
}
