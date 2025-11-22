<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OneTimeInvoiceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'residential_company_id' => $this->residential_company_id,
            'street_id' => $this->street_id,

            'invoice_number' => $this->invoice_number,
            'variable_symbol' => $this->variable_symbol,

            'invoice_name' => $this->invoice_name,
            'invoice_text' => $this->invoice_text,
            'invoice_above_table_text' => $this->invoice_above_table_text,

            'status' => $this->status,
            'issued_at' => $this->issued_at,
            'due_at' => $this->due_at,
            'delivered_at' => $this->delivered_at,
            'payment_date' => $this->payment_date,
            'billing_year' => $this->billing_year,
            'billing_month' => $this->billing_month,

            'residential_company_name' => $this->residential_company_name,
            'residential_company_city' => $this->residential_company_city,
            'residential_company_state' => $this->residential_company_state,
            'residential_company_address' => $this->residential_company_address,
            'residential_company_zip' => $this->residential_company_zip,
            'residential_company_ico' => $this->residential_company_ico,
            'residential_company_dic' => $this->residential_company_dic,
            'residential_company_ic_dph' => $this->residential_company_ic_dph,
            'residential_company_bank_account' => $this->residential_company_bank_account,
            'residential_company_bank_swift' => $this->residential_company_bank_swift,

            'invoice_issuer_name' => $this->invoice_issuer_name,
            'invoice_issuer_email' => $this->invoice_issuer_email,
            'invoice_issuer_phone' => $this->invoice_issuer_phone,
            'signature_base64' => $this->signature_base64,

            'subtotal' => $this->subtotal,
            'tax' => $this->tax,
            'total' => $this->total,
            'items' => $this->items,
            'qr_code' => $this->qr_code,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
