<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class PowerResource extends JsonResource
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
            'powerCode' => $this->code,
            'powerName' => $this->name,
            'powerSlogan' => $this->slogan,
            'powerImage' => url('/asset/profile').'/'.$this->image,
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
