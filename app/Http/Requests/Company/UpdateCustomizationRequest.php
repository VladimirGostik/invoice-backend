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
     */
    public function rules(): array
    {
        return [
            'invoice_issuer_name'  => ['required', 'string', 'max:255'],
            'invoice_issuer_email' => ['nullable', 'string', 'email', 'max:50'],
            'invoice_issuer_phone' => ['nullable', 'string', 'max:50'],
            'signature_base64'     => ['nullable', 'string'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Validácia base64 formátu
        if ($this->has('signature_base64') && !empty($this->signature_base64)) {
            // Odstráň prefix ak existuje (data:image/png;base64,...)
            $signature = $this->signature_base64;

            if (strpos($signature, 'data:image') === 0) {
                $signature = substr($signature, strpos($signature, ',') + 1);
            }

            $this->merge([
                'signature_base64' => $signature
            ]);
        }
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'signature_base64.string' => 'Signature must be a valid base64 string',
        ];
    }

    /**
     * Additional validation after standard validation
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->has('signature_base64') && !empty($this->signature_base64)) {
                $decoded = base64_decode($this->signature_base64, true);

                // Skontroluj či je to validný base64
                if ($decoded === false || base64_encode($decoded) !== $this->signature_base64) {
                    $validator->errors()->add('signature_base64', 'Invalid base64 format');
                    return;
                }

                // Skontroluj či je to obrázok
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->buffer($decoded);

                $allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/gif', 'image/webp'];

                if (!in_array($mimeType, $allowedTypes)) {
                    $validator->errors()->add('signature_base64', 'Signature must be a valid image (PNG, JPG, GIF, WEBP)');
                }

                // Skontroluj veľkosť (napr. max 2MB)
                $sizeInBytes = strlen($decoded);
                $maxSize = 2 * 1024 * 1024; // 2MB

                if ($sizeInBytes > $maxSize) {
                    $validator->errors()->add('signature_base64', 'Signature size must not exceed 2MB');
                }
            }
        });
    }
}
