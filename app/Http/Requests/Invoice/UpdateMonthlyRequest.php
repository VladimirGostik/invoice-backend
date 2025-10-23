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
            'items.*.unit' => ['required', 'string', 'max:50'],
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

            'invoice_text' => ['nullable', 'string'],

            'invoice_above_table_text' => ['sometimes', 'string'],
            'is_complex_billing' => ['required', 'boolean'],
            'additional_info_1' => ['nullable', 'string', 'max:255'],
            'additional_info_2' => ['nullable', 'string', 'max:255'],
            'info_dph' => ['nullable', 'string'],
        ];
    }
}
