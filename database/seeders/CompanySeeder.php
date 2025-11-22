<?php

namespace Database\Seeders;

use App\Enums\CompanyTypeEnum;
use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏢 Creating companies...');

        // 1 hlavná spoločnosť
        Company::factory()->create([
            'company_type' => CompanyTypeEnum::MAIN->value,
            'company_name' => 'ABC s.r.o.',
            'company_city' => 'Bratislava',
            'company_state' => 'Slovensko',
            'company_address' => 'Hlavná 123',
            'company_zip' => '81101',
            'company_ico' => '12345678',
            'company_dic' => '1234567890',
            'company_ic_dph' => 'SK1234567890',
            'company_bank_account' => 'SK0202000000005047157358',
            'company_bank_swift' => 'SUBASKBX',
        ]);

        // 10 bytových spoločností
        Company::factory()
            ->count(20)
            ->create([
                'company_type' => CompanyTypeEnum::RESIDENTIAL->value,
            ]);

        $this->command->info('✅ Created 1 main company and 10 residential companies');
    }
}
