<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MonthlyInvoiceListResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'main_company_name' => $this->company->company_name,
            'residential_company_id' => $this->residential_company_id,
            'company_name' => $this->company_name,
            'street_id' => $this->street_id,
            'street_name' => $this->whenLoaded('street', function() {
                return $this->street?->street_name;
            }),
            'invoice_name' => $this->invoice_name,
            'type' => $this->type,
            'subtotal' => $this->subtotal,
            'total' => $this->total,
        ];
    }
}
