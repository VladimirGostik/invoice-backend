<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\CompanyTypeEnum;

class Company extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $casts = [
        'company_type' => CompanyTypeEnum::class,
    ];

}
