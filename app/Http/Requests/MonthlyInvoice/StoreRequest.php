<?php
// filepath: /Users/vladimirgostik/osobne_projekty/invoice-backend/app/Http/Requests/MonthlyInvoice/StoreRequest.php

namespace App\Http\Requests\MonthlyInvoice;

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
            // ✅ IDs - kontrolujeme len existence v DB
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'residential_company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'street_id' => ['nullable', 'integer', 'exists:streets,id'],

            // Invoice info
            'invoice_name' => ['required', 'string', 'max:255'],

            // Items
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.unit' => ['required', 'string', 'max:50'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.line_total' => ['required', 'numeric', 'min:0'],

            // Totals
            'subtotal' => ['required', 'numeric', 'min:0'],
            'tax' => ['required', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],

            // Company snapshot (optional overrides)
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

    /**
     * ✅ Vlastné validačné správy
     */
    public function messages(): array
    {
        return [
            'company_id.exists' => 'Vybraná hlavná firma neexistuje.',
            'residential_company_id.exists' => 'Vybraná bytová spoločnosť neexistuje.',
            'street_id.exists' => 'Vybraná ulica neexistuje.',
            'items.required' => 'Faktúra musí mať aspoň 1 položku.',
            'items.*.description.required' => 'Popis položky je povinný.',
            'items.*.quantity.min' => 'Množstvo musí byť väčšie ako 0.',
        ];
    }
}
