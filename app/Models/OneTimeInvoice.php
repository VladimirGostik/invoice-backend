<?php

namespace App\Models;

use App\Enums\InvoiceTypeEnum;
use App\Enums\InvoiceStatusEnum;

class OneTimeInvoice extends Invoice
{
    protected $type = InvoiceTypeEnum::ONE_TIME->value;
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

    public function scopeOneTime($query)
    {
        return $query->where('type', InvoiceTypeEnum::ONE_TIME->value);
    }
}