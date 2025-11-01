<?php

namespace App\Models;

use App\Enums\InvoiceTypeEnum;
use App\Enums\InvoiceStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\InvoiceFactory;

class MonthlyInvoice extends Invoice
{
    use HasFactory;

    protected $type = InvoiceTypeEnum::MONTHLY->value;
    protected $table = 'invoices';

    protected $attributes = [
        'type' => 'monthly',
    ];

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope('monthly', function ($query) {
            $query->where('type', InvoiceTypeEnum::MONTHLY->value);
        });

        static::creating(function ($model) {
            $model->type = InvoiceTypeEnum::MONTHLY->value;
        });
    }

    /**
     * ✅ Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return InvoiceFactory::new()->monthly();
    }

    public function snapshot(): array
    {
        return $this->only([
            'company_id',
            'residential_company_id',
            'street_id',
            'street_id',
            'invoice_name',
            // company fields
            'company_name',
            'company_city',
            'company_state',
            'company_address',
            'company_zip',
            'company_ico',
            'company_dic',
            'company_ic_dph',
            'company_bank_account',
            'company_bank_swift',
            // custom fields
            'invoice_text',
            'is_complex_billing',
            'additional_info_1',
            'additional_info_2',
            'info_dph',
            'invoice_above_table_text',
            //totals
            'subtotal',
            'tax',
            'total',
        ]);
    }
}
