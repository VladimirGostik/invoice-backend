<?php

namespace App\Http\Requests\Invoice;


use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;

class CreateOneTimeFromMonthly extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'monthly_invoice_ids' => ['required', 'array', 'min:1'],
            'monthly_invoice_ids.*' => ['required', 'exists:invoices,id'],
            'issued_at' => ['required', 'date'],
            'due_at' => ['required', 'date', 'after_or_equal:issued_at'],
            'billing_year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'billing_month' => ['required', 'integer', 'min:1', 'max:12'],
        ];
    }
}
