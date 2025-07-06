<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // database/seeders/CompanySeeder.php
    public function run(): void
    {
        Company::factory()
                ->count(10)
                ->create();
    }
}