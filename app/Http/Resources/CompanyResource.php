<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       return array_merge(parent::toArray($request), [
            //'streets' => $this->streets,
            'invoice_issuer_name'  => $this->companyCustomization?->invoice_issuer_name,
            'invoice_issuer_email' => $this->companyCustomization?->invoice_issuer_email,
            'invoice_issuer_phone' => $this->companyCustomization?->invoice_issuer_phone,
            'signature_data_uri' => $this->companyCustomization?->getSignatureDataUriAttribute(),
        ]);
    }
}
