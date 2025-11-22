<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Company;
use App\Models\Street;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $invoiceId = $this->route('invoice')->id;
        $companyId = $this->input('company_id');

        return [
            'company_id' => ['sometimes', 'integer'],
            'residential_company_id' => ['nullable', 'integer'],
            'street_id' => ['nullable', 'integer'],
            'monthly_invoice_id' => ['nullable', 'integer'],

            'invoice_name' => ['sometimes', 'string', 'max:255'],
            'invoice_number' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('invoices', 'invoice_number')
                    ->where('company_id', $companyId)
                    ->ignore($invoiceId),
            ],
            'variable_symbol' => ['nullable', 'string', 'max:50'],
            'status' => ['sometimes', 'string', Rule::in(['draft', 'issued', 'paid', 'cancelled'])],

            'items' => ['sometimes', 'array', 'min:1'],
            'items.*.id' => ['sometimes', 'integer'],
            'items.*.description' => ['required_with:items', 'string', 'max:255'],
            'items.*.unit' => ['required_with:items', 'string', 'max:50'],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1'],
            'items.*.unit_price' => ['required_with:items', 'numeric', 'min:0'],
            'items.*.line_total' => ['required_with:items', 'numeric', 'min:0'],

            'subtotal' => ['sometimes', 'numeric', 'min:0'],
            'tax' => ['sometimes', 'numeric', 'min:0'],
            'total' => ['sometimes', 'numeric', 'min:0'],

            'issued_at' => ['sometimes', 'date'],
            'due_at' => ['sometimes', 'date', 'after_or_equal:issued_at'],
            'delivered_at' => ['nullable', 'date'],
            'payment_date' => ['nullable', 'date'],
            'billing_year' => ['sometimes', 'integer', 'min:2000', 'max:2100'],
            'billing_month' => ['sometimes', 'integer', 'min:1', 'max:12'],

            // Residential company snapshot
            'residential_company_name' => ['nullable', 'string', 'max:255'],
            'residential_company_city' => ['nullable', 'string', 'max:255'],
            'residential_company_state' => ['nullable', 'string', 'max:255'],
            'residential_company_address' => ['nullable', 'string', 'max:500'],
            'residential_company_zip' => ['nullable', 'string', 'max:20'],
            'residential_company_ico' => ['nullable', 'string', 'max:50'],
            'residential_company_dic' => ['nullable', 'string', 'max:50'],
            'residential_company_ic_dph' => ['nullable', 'string', 'max:50'],
            'residential_company_bank_account' => ['nullable', 'string', 'max:100'],
            'residential_company_bank_swift' => ['nullable', 'string', 'max:50'],

            'invoice_text' => ['nullable', 'string'],
            'is_complex_billing' => ['sometimes', 'boolean'],
            'additional_info_1' => ['nullable', 'string', 'max:255'],
            'additional_info_2' => ['nullable', 'string', 'max:255'],
            'info_dph' => ['nullable', 'string'],
            'invoice_above_table_text' => ['nullable', 'string'],
        ];
    }

    // ✅ Bulk validácia ID v jednom query
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Zbierka všetkých ID na validáciu
            $companyIds = array_filter([
                $this->input('company_id'),
                $this->input('residential_company_id'),
            ]);

            $streetId = $this->input('street_id');

            // Validuj companies v jednom query
            if (!empty($companyIds)) {
                $existingCompanyIds = Company::whereIn('id', $companyIds)
                    ->pluck('id')
                    ->toArray();

                if ($this->input('company_id') && !in_array($this->input('company_id'), $existingCompanyIds)) {
                    $validator->errors()->add('company_id', 'Selected company does not exist.');
                }

                if ($this->input('residential_company_id') && !in_array($this->input('residential_company_id'), $existingCompanyIds)) {
                    $validator->errors()->add('residential_company_id', 'Selected residential company does not exist.');
                }
            }

            // Validuj street
            if ($streetId && !Street::where('id', $streetId)->exists()) {
                $validator->errors()->add('street_id', 'Selected street does not exist.');
            }
        });
    }
}
