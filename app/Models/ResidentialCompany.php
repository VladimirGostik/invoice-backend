<?php
namespace App\Models;

use App\Enums\CompanyTypeEnum;
use Illuminate\Database\Eloquent\Builder;

class ResidentialCompany extends Company
{

    protected $table = 'companies';
    protected $guarded = ['id'];
    
    /**
     * Scope a query to only include residential companies.
     *
     * @param Builder $query
     * @return Builder
     */
     protected static function booted(): void
    {
        static::addGlobalScope('residential', function (Builder $query) {
            $query->where('company_type', CompanyTypeEnum::RESIDENTIAL->value);
        });

        static::creating(function (ResidentialCompany $model) {
            $model->company_type = CompanyTypeEnum::RESIDENTIAL->value;
        });
    }

}