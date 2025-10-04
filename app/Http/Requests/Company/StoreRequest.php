<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {        
        return [
            'company_name'         => ['required', 'string', 'max:255'],
            'company_city'         => ['required', 'string', 'max:255'],
            'company_state'        => ['required', 'string', 'max:255'],
            'company_address'      => ['required', 'string', 'max:500'],
            'company_zip'          => ['required', 'string', 'max:20'],
            'company_ico'          => ['nullable', 'string', 'max:50', 'unique:companies,company_ico'],
            'company_dic'          => ['nullable', 'string', 'max:50', 'unique:companies,company_dic'],
            'company_ic_dph'       => ['nullable', 'string', 'max:50', 'unique:companies,company_ic_dph'],
            'company_bank_account' => ['nullable', 'string', 'max:100'],
            'company_bank_swift'   => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_ico.unique'    => 'Toto IČO už v systéme existuje.',
            'company_dic.unique'    => 'Toto DIČ už v systéme existuje.',
            'company_ic_dph.unique' => 'Toto IČ-DPH už v systéme existuje.',
        ];
    }
}
