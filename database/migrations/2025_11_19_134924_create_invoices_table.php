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

            $table->foreignId('monthly_invoice_id')
                  ->nullable()
                  ->constrained('monthly_invoices');

            // Invoice metadata
            $table->string('invoice_number')->nullable();
            $table->string('variable_symbol')->nullable();
            $table->string('invoice_name');
            $table->enum('status', array_column(InvoiceStatusEnum::cases(), 'value'))->default(InvoiceStatusEnum::DRAFT->value);

            $table->unsignedSmallInteger('billing_year')->nullable();
            $table->unsignedTinyInteger('billing_month')->nullable();
            $table->date('issued_at')->nullable();
            $table->date('due_at')->nullable(); // new field
            $table->date('delivered_at')->nullable();
            $table->date('payment_date')->nullable();

            // Company fields
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

            // Customization options
            $table->string('invoice_text')->nullable();
            $table->boolean('is_complex_billing')->default(false); // new field
            $table->string('additional_info_1')->nullable(); // new field
            $table->string('additional_info_2')->nullable(); // new field
            $table->text('invoice_above_table_text')->nullable();

            // Totals
            $table->decimal('subtotal',12,2)->default(0);
            $table->decimal('tax',12,2)->default(0);
            $table->decimal('total',12,2)->default(0);

            $table->text('info_dph')->nullable(); // new field

            $table->string('invoice_issuer_name')->nullable();
            $table->string('invoice_issuer_email')->nullable();
            $table->string('invoice_issuer_phone')->nullable();
            $table->longText('signature_base64')->nullable();
            $table->text('qr_code')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['status']);
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
        Schema::dropIfExists('invoices');
    }
};
