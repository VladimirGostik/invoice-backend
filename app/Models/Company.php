<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\CompanyTypeEnum;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Company extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $casts = [
        'company_type' => CompanyTypeEnum::class,
    ];

    public function streets()
    {
        return $this->hasMany(Street::class, 'company_id');
    }

    public function companyCustomization(): HasOne
    {
        return $this->hasOne(CompanyCustomization::class);
    }

    public function signatures(): MorphOne
    {
        return $this->morphOne(File::class, 'fileable')->where('collection', 'signatures');
    }

    public function snapshot(): array
    {
        return [
            'company_name' => $this->company_name,
            'company_city' => $this->company_city,
            'company_state' => $this->company_state,
            'company_address' => $this->company_address,
            'company_zip' => $this->company_zip,
            'company_ico' => $this->company_ico,
            'company_dic' => $this->company_dic,
            'company_ic_dph' => $this->company_ic_dph,
            'company_bank_account' => $this->company_bank_account,
            'company_bank_swift' => $this->company_bank_swift,
            'signatures' => $this->signatures?->path,
        ];
    }
}