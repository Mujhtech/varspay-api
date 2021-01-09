<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class NewUserResource extends JsonResource
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
            'id' => $this->id,
            'username' => $this->username,
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'zip_code' => $this->zip_code,
            'account_number' => $this->acct_no,
            'account_reference' => $this->acct_ref,
            'pin' => $this->pin,
            'balance' => $this->balance,
            'password' => '12345678',
            'eth_address' => $this->eth_address,
            'email_verification' => ($this->email_verify) ? true : false,
            'phone_verification' => ($this->phone_verify) ? true : false,
            'kyc_status' => ($this->kyc_status) ? true : false,
            'joined_at' => Carbon::parse($this->created_at)->format('M d, Y')
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
