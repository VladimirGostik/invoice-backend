<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest; 

class UpdateMonthlyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required', 'exists:companies,id'],
            'residential_company_id' => ['nullable', 'exists:companies,id'],
            'street_id' => ['nullable', 'exists:streets,id'],
            'invoice_name' => ['required', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.line_total' => ['required', 'numeric', 'min:0'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'tax' => ['required', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
            'company_name'         => ['sometimes', 'string', 'max:255'],
            'company_city'         => ['sometimes', 'string', 'max:255'],
            'company_state'        => ['sometimes', 'string', 'max:255'],
            'company_address'      => ['sometimes', 'string', 'max:500'],
            'company_zip'          => ['sometimes', 'string', 'max:20'],
            'company_ico'          => ['sometimes', 'string', 'max:50'],
            'company_dic'          => ['sometimes', 'string', 'max:50'],
            'company_ic_dph'       => ['sometimes', 'string', 'max:50'],
            'company_bank_account' => ['sometimes', 'string', 'max:100'],
            'company_bank_swift'   => ['sometimes', 'string', 'max:50'],
            'invoice_text' => ['sometimes', 'string'],
            'custom_field_name1' => ['sometimes', 'string', 'max:255'],
            'custom_field_name2' => ['sometimes', 'string', 'max:255'],
            'custom_field_name3' => ['sometimes', 'string', 'max:255'],
            'custom_field_name4' => ['sometimes', 'string', 'max:255'],
            'custom_field_name5' => ['sometimes', 'string', 'max:255'],
            'invoice_above_table_text' => ['sometimes', 'string']
        ];
    }
}