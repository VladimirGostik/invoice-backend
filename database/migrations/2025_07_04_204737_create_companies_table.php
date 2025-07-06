<?php

use App\Enums\CompanyTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('company_city');
            $table->string('company_state');
            $table->string('company_address');
            $table->string('company_zip');
            $table->string('company_ico')->nullable();
            $table->string('company_dic')->nullable();
            $table->string('company_ic_dph')->nullable();
            $table->string('company_bank_account')->nullable();
            $table->string('company_bank_swift')->nullable();
            $table->enum('company_type', array_column(CompanyTypeEnum::cases(), 'value'))->default(CompanyTypeEnum::MAIN->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
