<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //$this->call(PermissionSeeder::class);

        $this->call(UserSeeder::class);

        $this->call(CompanySeeder::class);

        $this->call(StreetSeeder::class);

        $this->call(MonthlyInvoiceSeeder::class);

        // $this->command->info('6️⃣ Seeding one-time invoices...');
        // $this->call(OneTimeInvoiceSeeder::class);
    }
}
