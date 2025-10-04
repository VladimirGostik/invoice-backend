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

       $superAdmin = User::create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'phone' => '1234567890',
            'password' => Hash::make('password'),
            'email' => 'superadmin@example.com',
            'state' => UserStateEnum::ACTIVE,
        ]);
       $superAdmin->assignRole(UserRoleEnum::SUPER_ADMIN);

        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'Clovek',
            'phone' => '1234567890',
            'password' => Hash::make('password'),
            'email' => 'admin@example.com',
            'state' => UserStateEnum::ACTIVE,
        ]);

        $admin->assignRole(UserRoleEnum::ADMIN);
    }
}
