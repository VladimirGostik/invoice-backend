<?php
// filepath: /Users/vladimirgostik/osobne_projekty/invoice-backend/database/migrations/2024_11_11_100000_add_role_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role', 20)->default('user')->after('email');
                $table->index('role');
            }
        });

        // ✅ Update existujúcich users
        try {
            // Všetci users budú 'user'
            User::query()->update(['role' => 'user']);

            // Prvý user bude superadmin
            $firstUser = User::first();
            if ($firstUser) {
                $firstUser->update(['role' => 'superadmin']);
            }
        } catch (\Exception $e) {
            // Ignoruj chyby pri seedingu
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
