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

    /**
     * Get signature as data URI for PDF/frontend
     */
    public function getSignatureDataUriAttribute(): ?string
    {
        if (!$this->signature_base64) {
            return null;
        }

        // Zisti MIME type z base64
        $decoded = base64_decode($this->signature_base64);
        if ($decoded === false) {
            return null;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($decoded);

        return "data:{$mimeType};base64,{$this->signature_base64}";
    }

    /**
     * Check if invoice has signature
     */
    public function hasSignature(): bool
    {
        return !empty($this->signature_base64);
    }
}
