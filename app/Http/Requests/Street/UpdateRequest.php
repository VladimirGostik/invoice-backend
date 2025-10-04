<?php

namespace App\Http\Requests\Street;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\CompanyTypeEnum;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'street_name' => ['sometimes', 'string', 'max:255'],
            'company_id'  => [
                'sometimes',
                'integer',
                Rule::exists('companies', 'id')
                    ->where(fn($query) => 
                        $query->where('company_type', CompanyTypeEnum::RESIDENTIAL->value)
                    ),
            ],
        ];
    }
}
