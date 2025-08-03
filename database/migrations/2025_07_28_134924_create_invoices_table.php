<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\InvoiceTypeEnum;
use App\Enums\InvoiceStatusEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            // Issuer (main company)
            $table->foreignId('company_id')
                  ->constrained('companies')
                  ->cascadeOnDelete();

            // Recipient for residential: direct reference to residential company
            $table->foreignId('residential_company_id')
                  ->nullable()
                  ->constrained('companies')
                  ->cascadeOnDelete();

            // Recipient street (optional)
            $table->foreignId('street_id')
                  ->nullable()
                  ->constrained('streets')
                  ->cascadeOnDelete();

            // Invoice metadata
            $table->string('invoice_number')->nullable();
            $table->string('variable_symbol')->nullable();
            $table->string('invoice_name');
            $table->enum('type', array_column(InvoiceTypeEnum::cases(), 'value'));
            $table->enum('status', array_column(InvoiceStatusEnum::cases(), 'value'))->default(InvoiceStatusEnum::DRAFT->value);

            $table->unsignedSmallInteger('billing_year');
            $table->unsignedTinyInteger('billing_month');
            $table->date('issued_at')->nullable();
            $table->date('due_at')->nullable();
            $table->date('payment_date')->nullable();

            // Company fields
            $table->string('company_name')->nullable();
            $table->string('company_city')->nullable();
            $table->string('company_state')->nullable();
            $table->string('company_address')->nullable();
            $table->string('company_zip')->nullable();
            $table->string('company_ico')->nullable();
            $table->string('company_dic')->nullable();
            $table->string('company_ic_dph')->nullable();
            $table->string('company_bank_account')->nullable();
            $table->string('company_bank_swift')->nullable();

            // Customization options
            $table->text('invoice_text')->nullable();
            $table->string('residential_company_name')->nullable();
            $table->string('custom_field_name1')->nullable();
            $table->string('custom_field_name2')->nullable();
            $table->string('custom_field_name3')->nullable();
            $table->string('custom_field_name4')->nullable();
            $table->string('custom_field_name5')->nullable();
            $table->text('invoice_above_table_text')->nullable();

            // Totals
            $table->decimal('subtotal',12,2)->default(0);
            $table->decimal('tax',12,2)->default(0);
            $table->decimal('total',12,2)->default(0);

            $table->string('invoice_issuer_name')->nullable();
            $table->string('invoice_issuer_email')->nullable();
            $table->string('invoice_issuer_phone')->nullable();
            $table->string('signatures')->nullable();
            $table->text('qr_code')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['type','issued_at']);
            $table->index('company_id');
            $table->index('residential_company_id');
            $table->index('street_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
