<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\InvoiceStatusEnum;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        // tu môžeš spraviť vlastné autorizácie, napr. cez policy
        return true;
    }

    public function rules(): array
    {
        // Počítačové ID invoice z route-model-binding
        $invoiceId  = $this->route('invoice')->id;
        $companyId  = $this->input('company_id');

        return [
            'company_id'               => ['required', 'exists:companies,id'],
            'residential_company_id'   => ['nullable', 'exists:companies,id'],
            'street_id'                => ['nullable', 'exists:streets,id'],

            'invoice_name'             => ['required', 'string', 'max:255'],
            'invoice_number'           => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('invoices', 'invoice_number')
                    ->ignore($invoiceId)
                    ->where(fn($q) => $q->where('company_id', $companyId)),
            ],
            'status' => [
                'required',
                'string',
                Rule::in(array_map(fn($enum) => $enum->value, InvoiceStatusEnum::cases())),
            ],

            // polia pre položky faktúry – ak ich chceš povoliť aj pri update
            'items'                    => ['sometimes', 'array', 'min:1'],
            'items.*.description'      => ['required_with:items', 'string', 'max:255'],
            'items.*.quantity'         => ['required_with:items', 'integer', 'min:1'],
            'items.*.unit_price'       => ['required_with:items', 'numeric', 'min:0'],
            'items.*.line_total'       => ['required_with:items', 'numeric', 'min:0'],

            // celkové sumy
            'subtotal'                 => ['required', 'numeric', 'min:0'],
            'tax'                      => ['required', 'numeric', 'min:0'],
            'total'                    => ['required', 'numeric', 'min:0'],

            // dátumy
            'issued_at'                => ['required', 'date'],
            'due_at'                   => ['required', 'date', 'after_or_equal:issued_at'],
            'payment_date'             => ['nullable', 'date'],

            // mesačné faktúry
            'billing_year'             => ['required', 'integer', 'min:2000', 'max:2100'],
            'billing_month'            => ['required', 'integer', 'min:1', 'max:12'],

            // voliteľné prepisy údajov
            'company_name'             => ['nullable', 'string', 'max:255'],
            'company_city'             => ['nullable', 'string', 'max:255'],
            'company_state'            => ['nullable', 'string', 'max:255'],
            'company_address'          => ['nullable', 'string', 'max:500'],
            'company_zip'              => ['nullable', 'string', 'max:20'],
            'company_ico'              => ['nullable', 'string', 'max:50'],
            'company_dic'              => ['nullable', 'string', 'max:50'],
            'company_ic_dph'           => ['nullable', 'string', 'max:50'],
            'company_bank_account'     => ['nullable', 'string', 'max:100'],
            'company_bank_swift'       => ['nullable', 'string', 'max:50'],

            // ďalšie textové polia
            'invoice_text'             => ['nullable', 'string'],
            'custom_field_name1'       => ['nullable', 'string', 'max:255'],
            'custom_field_name2'       => ['nullable', 'string', 'max:255'],
            'custom_field_name3'       => ['nullable', 'string', 'max:255'],
            'custom_field_name4'       => ['nullable', 'string', 'max:255'],
            'custom_field_name5'       => ['nullable', 'string', 'max:255'],
            'invoice_above_table_text' => ['nullable', 'string'],
        ];
    }
}
