<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BankCollectionResource extends JsonResource
{
    
    public static $wrap = 'responseBody';
  
  
    public function toArray($request)
    {
        return [
            'bankCode' => $this['bankcode'],
            'bankName' => $this['bankname']
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
