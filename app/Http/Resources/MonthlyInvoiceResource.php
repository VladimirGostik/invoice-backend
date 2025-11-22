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
            'invoice_text' => $this->invoice_text,
            'invoice_above_table_text' => $this->invoice_above_table_text,
            'is_complex_billing' => $this->is_complex_billing,
            'additional_info_1' => $this->additional_info_1,
            'additional_info_2' => $this->additional_info_2,
            'info_dph' => $this->info_dph,
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
            'subtotal' => $this->subtotal,
            'tax' => $this->tax,
            'total' => $this->total,
            'items' => $this->items,
        ];
    }
}
