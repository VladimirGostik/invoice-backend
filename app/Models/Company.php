<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\CompanyTypeEnum;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function signatures()
    {
        return $this->morphMany(File::class, 'fileable')->where('collection', 'signatures');
    }
}