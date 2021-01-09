<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class VirtualAlertCollectionResource extends JsonResource
{
    
    public static $wrap = 'responseBody';
  
  
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'amount' => $this->amount,
            'charges' => $this->charges,
            'status' => ($this->status == 1) ? 'success' : 'pending',
            'created_at' => Carbon::parse($this->created_at)->format('M d, Y'),
            'updated_at' => Carbon::parse($this->updated_at)->format('M d, Y')
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
