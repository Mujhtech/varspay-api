<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class AlertCollectionResource extends JsonResource
{
    
    public static $wrap = 'responseBody';
  
  
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'type' => ($this->type == 1) ? 'Debit' : 'Credit',
            'amount' => $this->amount,
            'status' => ($this->status == 1) ? 'success' : 'pending',
            'content' => $this->details,
            'created_at' => Carbon::parse($this->updated_at)->format('M d, Y')
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
