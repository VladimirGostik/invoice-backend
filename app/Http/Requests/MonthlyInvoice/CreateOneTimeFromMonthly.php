<?php

namespace App\Http\Requests\MonthlyInvoice;

use Illuminate\Foundation\Http\FormRequest;

class CreateOneTimeFromMonthlyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'monthly_invoice_ids' => ['required', 'array', 'min:1'],
            'monthly_invoice_ids.*' => ['required', 'exists:monthly_invoices,id'],
            'issued_at' => ['required', 'date'],
            'due_at' => ['required', 'date', 'after_or_equal:issued_at'],
            'delivered_at' => ['required', 'date'],
        ];
    }
}
