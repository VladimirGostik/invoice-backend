<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Street extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    /**
     * Only residential companies are ever linked,
     * since ResidentialCompany has a global scope.
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
