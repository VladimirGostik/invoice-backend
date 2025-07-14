<?php

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
        Schema::create('company_customizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->index()->constrained('companies');
            $table->string('invoice_issuer_name')->nullable();
            $table->string('invoice_issuer_email')->nullable();
            $table->string('invoice_issuer_phone')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_customizations');
    }
};
