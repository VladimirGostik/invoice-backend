<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MonthlyInvoiceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'residential_company_id' => $this->residential_company_id,
            'street_id' => $this->street_id,
            'invoice_name' => $this->invoice_name,
            'type' => $this->type,
            'status' => $this->status,
            'invoice_text' => $this->invoice_text,
            'invoice_above_table_text' => $this->invoice_above_table_text,
            'is_complex_billing' => $this->is_complex_billing,
            'additional_info_1' => $this->additional_info_1,
            'additional_info_2' => $this->additional_info_2,
            'info_dph' => $this->info_dph,
            'company_name' => $this->company_name,
            'company_city' => $this->company_city,
            'company_state' => $this->company_state,
            'company_address' => $this->company_address,
            'company_zip' => $this->company_zip,
            'company_ico' => $this->company_ico,
            'company_dic' => $this->company_dic,
            'company_ic_dph' => $this->company_ic_dph,
            'company_bank_account' => $this->company_bank_account,
            'company_bank_swift' => $this->company_bank_swift,
            'invoice_issuer_name' => $this->invoice_issuer_name,
            'invoice_issuer_email' => $this->invoice_issuer_email,
            'invoice_issuer_phone' => $this->invoice_issuer_phone,
            'signature_base64' => $this->signature_base64,
            'subtotal' => $this->subtotal,
            'tax' => $this->tax,
            'total' => $this->total,
            'items' => $this->items,
        ];
    }
}
