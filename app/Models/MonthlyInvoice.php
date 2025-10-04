<?php

namespace App\Models;

use App\Enums\InvoiceTypeEnum;
use App\Enums\InvoiceStatusEnum;

class MonthlyInvoice extends Invoice
{
    protected $type = InvoiceTypeEnum::MONTHLY->value;
    protected $table = 'invoices';

    protected $attributes = [
        'type' => InvoiceTypeEnum::MONTHLY->value,
        'status' => InvoiceStatusEnum::DRAFT->value,
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->type = InvoiceTypeEnum::MONTHLY->value;
            $model->status = InvoiceStatusEnum::DRAFT->value;
        });
    }

    public function scopeMonthly($query)
    {
        return $query->where('type', InvoiceTypeEnum::MONTHLY->value);
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
            'residential_company_name',
            'custom_field_name1',
            'custom_field_name2',
            'custom_field_name3',
            'custom_field_name4',
            'custom_field_name5',
            'invoice_above_table_text',
            //totals 
            'subtotal',
            'tax',
            'total',
        ]);
    }
}