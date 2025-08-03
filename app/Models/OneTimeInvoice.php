<?php

namespace App\Models;

use App\Enums\InvoiceTypeEnum;
use App\Enums\InvoiceStatusEnum;

class OneTimeInvoice extends Invoice
{
    protected $type = 'ONE_TIME';
    protected $table = 'invoices';

    protected $attributes = [
        'type' => InvoiceTypeEnum::ONE_TIME->value,
        'status' => InvoiceStatusEnum::DRAFT->value,
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->type = InvoiceTypeEnum::ONE_TIME->value;
            $model->status = InvoiceStatusEnum::DRAFT->value;
        });
    }
}