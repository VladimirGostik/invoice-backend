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

    public function scopeMainCompany($query)
    {
        return $query->where('company_type', CompanyTypeEnum::MAIN->value);
    }

    public function scopeResidentialCompany($query)
    {
        return $query->where('company_type', CompanyTypeEnum::RESIDENTIAL->value);
    }

    public function snapshot(): array
    {
        return [
            'residential_company_name' => $this->company_name,
            'residential_company_city' => $this->company_city,
            'residential_company_state' => $this->company_state,
            'residential_company_address' => $this->company_address,
            'residential_company_zip' => $this->company_zip,
            'residential_company_ico' => $this->company_ico,
            'residential_company_dic' => $this->company_dic,
            'residential_company_ic_dph' => $this->company_ic_dph,
            'residential_company_bank_account' => $this->company_bank_account,
            'residential_company_bank_swift' => $this->company_bank_swift,
        ];
    }
}
