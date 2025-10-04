<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * The invoice that this item belongs to.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    // /**
    //  * Calculate the total for this item.
    //  */
    // public function calculateTotal(): float
    // {
    //     return $this->quantity * $this->unit_price;
    // }
}