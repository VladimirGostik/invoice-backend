<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OneTimeInvoiceListResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'main_company_name' => $this->company?->company_name,
            'residential_company_id' => $this->residential_company_id,
            'residential_company_name' => $this->residential_company_name,
            'street_id' => $this->street_id,
            'street_name' => $this->street?->name,

            'invoice_number' => $this->invoice_number,

            'invoice_name' => $this->invoice_name,

            'status' => $this->status,
            'issued_at' => $this->issued_at,
            'due_at' => $this->due_at,

            'billing_year' => $this->billing_year,
            'billing_month' => $this->billing_month,

            'total' => $this->total,
        ];
    }
}
