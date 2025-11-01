<?php

namespace App\Models;

use App\Enums\InvoiceTypeEnum;
use App\Enums\InvoiceStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\InvoiceFactory;

class OneTimeInvoice extends Invoice
{
    use HasFactory;

    protected $type = InvoiceTypeEnum::ONE_TIME->value;
    protected $table = 'invoices';

    protected $attributes = [
        'type' => InvoiceTypeEnum::ONE_TIME->value,
        'status' => InvoiceStatusEnum::DRAFT->value,
    ];

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope('one_time', function ($query) {
            $query->where('type', InvoiceTypeEnum::ONE_TIME->value);
        });

        static::creating(function ($model) {
            $model->type = InvoiceTypeEnum::ONE_TIME->value;
        });
    }

    /**
     * ✅ Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return InvoiceFactory::new()->oneTime();
    }
}
