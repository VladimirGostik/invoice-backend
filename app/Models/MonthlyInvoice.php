<?php

namespace App\Models;

use App\Enums\InvoiceTypeEnum;
use App\Enums\InvoiceStatusEnum;

class MonthlyInvoice extends Invoice
{
    protected $type = 'MONTHLY';
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
}