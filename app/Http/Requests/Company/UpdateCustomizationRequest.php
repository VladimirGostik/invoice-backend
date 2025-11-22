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
        if ($this->has('signature_base64') && !empty($this->signature_base64)) {
            $signature = $this->signature_base64;

            // ✅ Odstráň prefix ak existuje
            if (strpos($signature, 'data:image') === 0) {
                $signature = substr($signature, strpos($signature, ',') + 1);
            }

            // ✅ Trim whitespace
            $signature = trim($signature);

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
                $signature = trim($this->signature_base64);

                // ✅ Skontroluj či je to validný base64
                $decoded = base64_decode($signature, true);

                if ($decoded === false) {
                    $validator->errors()->add('signature_base64', 'Invalid base64 format');
                    return;
                }

                // ✅ ODSTRÁNENÉ: base64_encode($decoded) !== $signature
                // Toto zlyhá kvôli whitespace a line breaks v base64

                // Skontroluj veľkosť najprv (pred MIME type check)
                $sizeInBytes = strlen($decoded);
                $maxSize = 2 * 1024 * 1024; // 2MB

                if ($sizeInBytes > $maxSize) {
                    $validator->errors()->add('signature_base64', 'Signature size must not exceed 2MB');
                    return;
                }

                // Skontroluj či je to obrázok
                try {
                    $finfo = new \finfo(FILEINFO_MIME_TYPE);
                    $mimeType = $finfo->buffer($decoded);

                    $allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/gif', 'image/webp', 'image/svg+xml'];

                    if (!in_array($mimeType, $allowedTypes)) {
                        $validator->errors()->add('signature_base64', 'Signature must be a valid image (PNG, JPG, GIF, WEBP, SVG)');
                    }
                } catch (\Exception $e) {
                    $validator->errors()->add('signature_base64', 'Could not validate image format');
                }
            }
        });
    }
}
