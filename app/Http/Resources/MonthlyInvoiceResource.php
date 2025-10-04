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
            'custom_field_name1' => $this->custom_field_name1,
            'custom_field_name2' => $this->custom_field_name2,
            'custom_field_name3' => $this->custom_field_name3,
            'custom_field_name4' => $this->custom_field_name4,
            'custom_field_name5' => $this->custom_field_name5,
            'invoice_above_table_text' => $this->invoice_above_table_text,
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
            'signatures' => $this->signatures,
            'subtotal' => $this->subtotal,
            'tax' => $this->tax,
            'total' => $this->total,
            'items' => $this->items,
        ];
    }
}