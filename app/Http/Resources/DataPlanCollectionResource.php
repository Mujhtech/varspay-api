<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DataPlanCollectionResource extends JsonResource
{
    
    public static $wrap = 'responseBody';
  
  
    public function toArray($request)
    {
        return [
            'planName' => $this->name,
            'planProviderCode' => $this->code,
            'planCode' => $this->plan,
            'planAmount' => $this->cost,
            'planSME' => ($this->sme) ? true : false
        ];
    }

    public function with($request)
    {
        return [
            'responseCode' => 200,
            'responseMessage' => 'success'
        ];
    }
}
