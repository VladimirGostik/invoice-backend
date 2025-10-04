<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use PHPUnit\Event\Telemetry\Snapshot;

class CompanyCustomization extends Model
{
    protected $guarded = ['id'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

     public function snapshot(): array
     {
        return [
            'invoice_issuer_name'    => $this->invoice_issuer_name,
            'invoice_issuer_email'   => $this->invoice_issuer_email,
            'invoice_issuer_phone'   => $this->invoice_issuer_phone,
            'signatures'             => $this->company->signatures?->path,
        ];
     }

}
