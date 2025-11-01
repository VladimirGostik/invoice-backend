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
        // ✅ Spúšťaj seedery jeden po druhom s výpisom
        $this->command->info('1️⃣ Seeding permissions...');
        $this->call(PermissionSeeder::class);

        $this->command->info('2️⃣ Seeding users...');
        $this->call(UserSeeder::class);

        $this->command->info('3️⃣ Seeding companies...');
        $this->call(CompanySeeder::class);

        $this->command->info('4️⃣ Seeding streets...');
        $this->call(StreetSeeder::class);

        $this->command->info('5️⃣ Seeding monthly invoices...');
        $this->call(MonthlyInvoiceSeeder::class);

        // $this->command->info('6️⃣ Seeding one-time invoices...');
        // $this->call(OneTimeInvoiceSeeder::class);

        $this->command->info('✅ Database seeding completed successfully!');
    }
}
