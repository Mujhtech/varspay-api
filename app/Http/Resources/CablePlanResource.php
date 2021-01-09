<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class CablePlanResource extends JsonResource
{
    
    public static $wrap = 'responseBody';
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'cableCode' => $this->code,
            'planCode' => $this->plan,
            'planName' => $this->name,
            'planAmount' => $this->cost,
            'currencyCode' => 'NGN',
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
