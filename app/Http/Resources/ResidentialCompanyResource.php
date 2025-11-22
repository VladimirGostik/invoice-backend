<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResidentialCompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company_name' => $this->company_name,
            'company_city' => $this->company_city,
            'company_state' => $this->company_state,
            'company_address' => $this->company_address,
            'company_zip' => $this->company_zip,
            'company_ico' => $this->company_ico,
            'company_dic' => $this->company_dic,
            'company_ic_dph' => $this->company_ic_dph,
            'company_type' => $this->company_type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'streets' => StreetResource::collection($this->whenLoaded('streets')),
        ];
    }
}
