<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\InvoiceStatusEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('monthly_invoices', function (Blueprint $table) {
            $table->id();

            // Issuer (main company)
            $table->foreignId('company_id')
                  ->constrained('companies')
                  ->cascadeOnDelete();

            // Recipient (residential company)
            $table->foreignId('residential_company_id')
                  ->nullable()
                  ->constrained('companies')
                  ->cascadeOnDelete();

            // Street (optional)
            $table->foreignId('street_id')
                  ->nullable()
                  ->constrained('streets')
                  ->cascadeOnDelete();

            // Invoice metadata
            $table->string('invoice_name');

            // Company snapshot fields
            $table->string('residential_company_name')->nullable();
            $table->string('residential_company_city')->nullable();
            $table->string('residential_company_state')->nullable();
            $table->string('residential_company_address')->nullable();
            $table->string('residential_company_zip')->nullable();
            $table->string('residential_company_ico')->nullable();
            $table->string('residential_company_dic')->nullable();
            $table->string('residential_company_ic_dph')->nullable();
            $table->string('residential_company_bank_account')->nullable();
            $table->string('residential_company_bank_swift')->nullable();

            // Customization
            $table->string('invoice_text')->nullable();
            $table->boolean('is_complex_billing')->default(false);
            $table->string('additional_info_1')->nullable();
            $table->string('additional_info_2')->nullable();
            $table->text('invoice_above_table_text')->nullable();
            $table->text('info_dph')->nullable();

            // Issuer info
            // $table->string('invoice_issuer_name')->nullable();
            // $table->string('invoice_issuer_email')->nullable();
            // $table->string('invoice_issuer_phone')->nullable();
            // $table->longText('signature_base64')->nullable();

            // Totals
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            $table->timestamps();

            // Indexes
            $table->index('company_id');
            $table->index('residential_company_id');
            $table->index('street_id');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_invoices');
    }
};
