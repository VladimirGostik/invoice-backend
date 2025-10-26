<?php

namespace App\Http\Requests\Invoice;

use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;

class StoreMonthlyRequest extends FormRequest
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
            'company_name' => ['nullable', 'string', 'max:255'],
            'company_city' => ['nullable', 'string', 'max:255'],
            'company_state' => ['nullable', 'string', 'max:255'],
            'company_address' => ['nullable', 'string', 'max:500'],
            'company_zip' => ['nullable', 'string', 'max:20'],
            'company_ico' => ['nullable', 'string', 'max:50'],
            'company_dic' => ['nullable', 'string', 'max:50'],
            'company_ic_dph' => ['nullable', 'string', 'max:50'],
            'invoice_text' => ['nullable', 'string'],
            'invoice_above_table_text' => ['sometimes', 'string'],
            'is_complex_billing' => ['required', 'boolean'],
            'additional_info_1' => [
                'nullable',
                'string',
                'max:255',
                'prohibited_if:is_complex_billing,false',
            ],
            'additional_info_2' => [
                'nullable',
                'string',
                'max:255',
                'prohibited_if:is_complex_billing,false',
            ],
            'info_dph' => ['nullable', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->is_complex_billing === false || $this->is_complex_billing === '0' || $this->is_complex_billing === 0) {
            $this->merge([
                'additional_info_1' => null,
                'additional_info_2' => null,
            ]);
        }
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'additional_info_1.prohibited_if' => 'Doplnková informácia 1 musí byť prázdna, keď nie je zapnuté komplexné fakturovanie.',
            'additional_info_2.prohibited_if' => 'Doplnková informácia 2 musí byť prázdna, keď nie je zapnuté komplexné fakturovanie.',
        ];
    }
}
