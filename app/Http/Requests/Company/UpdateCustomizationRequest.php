<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomizationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'invoice_issuer_name'  => ['required','string','max:255'],
            'invoice_issuer_email' => ['nullable','string','email','max:50'],
            'invoice_issuer_phone' => ['nullable','string','max:50'],
            'signatures'            => 'required|file|max:2048',
        ];
    }
}
