<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MonthlyInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'monthly_invoices';
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_complex_billing' => 'boolean',
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function residentialCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'residential_company_id');
    }

    public function street(): BelongsTo
    {
        return $this->belongsTo(Street::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(MonthlyInvoiceItem::class);
    }

    // Helpers
    public function getSignatureDataUriAttribute(): ?string
    {
        if (!$this->signature_base64) {
            return null;
        }

        $decoded = base64_decode($this->signature_base64);
        if ($decoded === false) {
            return null;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($decoded);

        return "data:{$mimeType};base64,{$this->signature_base64}";
    }

    public function hasSignature(): bool
    {
        return !empty($this->signature_base64);
    }

    public function snapshot(): array
    {
        return $this->only([
            'company_id',
            'residential_company_id',
            'street_id',
            'invoice_name',
            'residential_company_name',
            'residential_company_city',
            'residential_company_state',
            'residential_company_address',
            'residential_company_zip',
            'residential_company_ico',
            'residential_company_dic',
            'residential_company_ic_dph',
            'residential_company_bank_account',
            'residential_company_bank_swift',
            'invoice_text',
            'is_complex_billing',
            'additional_info_1',
            'additional_info_2',
            'info_dph',
            'invoice_above_table_text',
            'subtotal',
            'tax',
            'total',
        ]);
    }
}
