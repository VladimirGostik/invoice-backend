<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        $companyId = $this->input('company_id');

        return [
            'company_id' => ['required', 'exists:companies,id'],
            'residential_company_id' => ['nullable', 'exists:companies,id'],
            'street_id' => ['nullable', 'exists:streets,id'],
            'invoice_name' => ['required', 'string', 'max:255'],
            'invoice_number' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('invoices', 'invoice_number')
                    ->where(fn($query) => $query->where('company_id', $companyId)),
            ],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.line_total' => ['required', 'numeric', 'min:0'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'tax' => ['required', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
            'issued_at' => ['required', 'date'],
            'due_at' => ['required', 'date', 'after_or_equal:issued_at'],
            'payment_date' => ['nullable', 'date'],
            'billing_year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'billing_month' => ['required', 'integer', 'min:1', 'max:12'],
            'company_name'         => ['nullable', 'string', 'max:255'],
            'company_city'         => ['nullable', 'string', 'max:255'],
            'company_state'        => ['nullable', 'string', 'max:255'],
            'company_address'      => ['nullable', 'string', 'max:500'],
            'company_zip'          => ['nullable', 'string', 'max:20'],
            'company_ico'          => ['nullable', 'string', 'max:50'],
            'company_dic'          => ['nullable', 'string', 'max:50'],
            'company_ic_dph'       => ['nullable', 'string', 'max:50'],
            'company_bank_account' => ['nullable', 'string', 'max:100'],
            'company_bank_swift'   => ['nullable', 'string', 'max:50'],
            'invoice_text' => ['nullable', 'string'],
            'custom_field_name1' => ['nullable', 'string', 'max:255'],
            'custom_field_name2' => ['nullable', 'string', 'max:255'],
            'custom_field_name3' => ['nullable', 'string', 'max:255'],
            'custom_field_name4' => ['nullable', 'string', 'max:255'],
            'custom_field_name5' => ['nullable', 'string', 'max:255'],
            'invoice_above_table_text' => ['nullable', 'string']
        ];
    }
}