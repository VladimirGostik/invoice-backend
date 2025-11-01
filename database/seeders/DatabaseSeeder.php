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
        $this->call([
            PermissionSeeder::class,      // Používatelia a práva
            UserSeeder::class,             // Používatelia
            CompanySeeder::class,          // Spoločnosti (MAIN + RESIDENTIAL)
            StreetSeeder::class,           // Ulice pre bytové spoločnosti
            MonthlyInvoiceSeeder::class,   // Mesačné faktúry
            //OneTimeInvoiceSeeder::class,   // ✅ Jednorazové faktúry (voliteľné)
        ]);
    }
}
