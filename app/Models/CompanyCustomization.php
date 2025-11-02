<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyCustomization extends Model
{
    protected $guarded = ['id'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get signature as data URI
     */
    public function getSignatureDataUriAttribute(): ?string
    {
        if (!$this->signature_base64) {
            return null;
        }

        // Zisti MIME type z base64
        $decoded = base64_decode($this->signature_base64);
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($decoded);

        return "data:{$mimeType};base64,{$this->signature_base64}";
    }

    public function snapshot(): array
    {
        return [
            'invoice_issuer_name'    => $this->invoice_issuer_name,
            'invoice_issuer_email'   => $this->invoice_issuer_email,
            'invoice_issuer_phone'   => $this->invoice_issuer_phone,
            'signature_base64'       => $this->signature_base64,
            'signature_data_uri'     => $this->signature_data_uri,
        ];
    }
}
