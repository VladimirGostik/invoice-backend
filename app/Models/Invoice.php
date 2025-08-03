<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'invoices';

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function residentialCompany()
    {
        return $this->belongsTo(Company::class, 'residential_company_id');
    }

    public function street()
    {
        return $this->belongsTo(Street::class, 'street_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }
}