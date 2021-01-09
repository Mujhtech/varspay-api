<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VirtualAccountResource extends JsonResource
{
    
    public static $wrap = 'responseBody';
  
  
    public function toArray($request)
    {
        return [
            'accountName' => $this->acct_no,
            'accountNumber' => $this->acct_name,
            'bankcode' => $this->bankcode,
            'bankname' => "Rubies Microfinance Bank"
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
