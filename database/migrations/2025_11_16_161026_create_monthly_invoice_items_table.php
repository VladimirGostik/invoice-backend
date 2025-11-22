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
        Schema::create('monthly_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monthly_invoice_id')
                  ->constrained('monthly_invoices')
                  ->cascadeOnDelete();

            $table->string('description');
            $table->string('unit')->nullable();
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('line_total', 12, 2);

            $table->timestamps();
            $table->softDeletes();

            $table->index('monthly_invoice_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_invoice_items');
    }
};
