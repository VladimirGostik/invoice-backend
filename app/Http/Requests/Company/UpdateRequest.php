<?php
namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Ak route('company') ešte nie je nastavené, nastavíme $companyId na null
        $company = $this->route('company');
        $companyId = $company instanceof \App\Models\Company
            ? $company->id
            : null;

        return [
            'company_name'         => ['required','string','max:255'],
            'company_city'         => ['required','string','max:255'],
            'company_state'        => ['required','string','max:255'],
            'company_address'      => ['required','string','max:500'],
            'company_zip'          => ['required','string','max:20'],
            'company_ico'          => array_filter([
                'required','string','max:50',
                $companyId
                    ? Rule::unique('companies','company_ico')->ignore($companyId)
                    : 'unique:companies,company_ico'
            ]),
            'company_dic'          => array_filter([
                'nullable','string','max:50',
                $companyId
                    ? Rule::unique('companies','company_dic')->ignore($companyId)
                    : 'unique:companies,company_dic'
            ]),
            'company_ic_dph'       => array_filter([
                'nullable','string','max:50',
                $companyId
                    ? Rule::unique('companies','company_ic_dph')->ignore($companyId)
                    : 'unique:companies,company_ic_dph'
            ]),
            'company_bank_account' => ['required','string','max:100'],
            'company_bank_swift'   => ['required','string','max:50'],
        ];
    }
}