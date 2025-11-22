<?php

namespace Database\Seeders;

use App\Enums\UserRoleEnum;
use App\Enums\UserStateEnum;
use App\Models\Entity;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
//        Schema::disableForeignKeyConstraints();
//
//        DB::table('users')->truncate();
//        DB::table('model_has_roles')->truncate();
//
//        Schema::enableForeignKeyConstraints();

        User::updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'phone' => '1234567890',
                'password' => Hash::make('password'),
                'state' => UserStateEnum::ACTIVE,
                'role' => UserRoleEnum::SUPER_ADMIN,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'Clovek',
                'phone' => '1234567890',
                'password' => Hash::make('password'),
                'state' => UserStateEnum::ACTIVE,
                'role' => UserRoleEnum::ADMIN,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'first_name' => 'Regular',
                'last_name' => 'User',
                'phone' => '5555555555',
                'password' => Hash::make('password'),
                'state' => UserStateEnum::ACTIVE,
                'role' => UserRoleEnum::USER,
                'email_verified_at' => now(),
            ]
        );
    }
}
