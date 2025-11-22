<?php
// filepath: /Users/vladimirgostik/osobne_projekty/invoice-backend/app/Http/Requests/MonthlyInvoice/UpdateRequest.php

namespace App\Http\Requests\MonthlyInvoice;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // IDs
            'company_id' => ['sometimes', 'exists:companies,id'],
            'residential_company_id' => ['nullable', 'exists:companies,id'],
            'street_id' => ['nullable', 'exists:streets,id'],

            // Invoice info
            'invoice_name' => ['sometimes', 'string', 'max:255'],

            // Items
            'items' => ['sometimes', 'array', 'min:1'],
            'items.*.description' => ['required_with:items', 'string', 'max:255'],
            'items.*.unit' => ['required_with:items', 'string', 'max:50'],
            'items.*.quantity' => ['required_with:items', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required_with:items', 'numeric', 'min:0'],
            'items.*.line_total' => ['required_with:items', 'numeric', 'min:0'],

            // Totals
            'subtotal' => ['sometimes', 'numeric', 'min:0'],
            'tax' => ['sometimes', 'numeric', 'min:0'],
            'total' => ['sometimes', 'numeric', 'min:0'],

            // Company snapshot
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

            // Customization
            'invoice_text' => ['nullable', 'string'],
            'invoice_above_table_text' => ['nullable', 'string'],
            'is_complex_billing' => ['sometimes', 'boolean'],
            'additional_info_1' => ['nullable', 'string', 'max:255'],
            'additional_info_2' => ['nullable', 'string', 'max:255'],
            'info_dph' => ['nullable', 'string'],

            // // Issuer info
            // 'invoice_issuer_name' => ['nullable', 'string', 'max:255'],
            // 'invoice_issuer_email' => ['nullable', 'email', 'max:255'],
            // 'invoice_issuer_phone' => ['nullable', 'string', 'max:50'],
            // 'signature_base64' => ['nullable', 'string'],
        ];
    }
}
