<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Carbon\Carbon;
use App\Lib\Providus;
use App\Models\ApiKey;
use App\Models\User;
use App\Lib\Rubies;
use App\Http\Resources\BankCollectionResource;
use App\Events\ApiSystemLogEvent;



class BankController extends Controller
{
    
    public $providus;
    
    public $rubies;
    
    public $api_key;
    
    private $authorized_user;

        
    public function __construct(Request $request)
    {
        $this->providus = new Providus;
        $this->rubies = new Rubies;
        
        if($request->header('authorization')){
            
            $prefix = explode(' ', $request->header('authorization'))[0];
            
            if($prefix === "Bearer"){
                
                $this->api_key = explode(' ', $request->header('authorization'))[1];
                $this->authorized_user = ApiKey::where('apikey', $this->api_key)->first()->user_id;
                event(new ApiSystemLogEvent("Initiated api server", $this->authorized_user));
                
            }
            
        }
        
    }

        
    public function index()
    {
        $banks = $this->rubies->bankList()["banklist"];
        
        if ($banks) {
            event(new ApiSystemLogEvent("Fetch all banks", $this->authorized_user));
            return BankCollectionResource::collection($banks)->additional([
                'responseMessage' => 'successful',
                'responseCode' => 200]);
            
        }

        return response()->json([
            'responseMessage' => 'Resource not found.',
            'responseCode' => 404
        ], 404);
        
    }
    
    public function resolveAccount(Request $request){
        
        $acct_name = $this->rubies->nameEnquries($request->input('accountnumber'), $request->input('bankcode'));
        
        if($acct_name["responsecode"] == "00" && $acct_name["responsemessage"] == "success"){
            event(new ApiSystemLogEvent("Fetch account details", $this->authorized_user));
            return response()->json([
                'accountname' => $acct_name["accountname"],
                'accountnumber' => $request->input('accountnumber'),
                'bankcode' => $request->input('bankcode'),
                'bankname' => $this->rubies->bankName($request->input('bankcode')),
                'responseMessage' => 'successful',
                'responseCode' => 200
            ], 200);
            
        }
        
        
        return response()->json([
            'responseMessage' => 'Resource not found.',
            'responseCode' => 404
        ], 404);
    }
    
    
    public function resolveBvn(Request $request){
        
        $set=Settings::first();
        $amountx="25";
        $user=$data['user']=User::find($this->authorized_user);
        
        if($user->balance>$amountx || $user->balance==$amountx){
        
            $acct_name = $this->rubies->verifyBVN($request->input('bvn'), round(microtime(true)));
            
            if($acct_name["responsecode"] == "00" && $acct_name["responsemessage"] == "successful"){
                event(new ApiSystemLogEvent("Fetch bvn details", $this->authorized_user));
                $b=$user->balance-$amountx;
                $user->balance=$b;
                $user->save();
                
                return response()->json([
                    'bvn' => $request->input('bvn'),
                    'firstname' => $acct_name["data"]["firstName"],
                    'lastname' => $acct_name["data"]["lastName"],
                    'middlename' => $acct_name["data"]["middleName"],
                    'dob' => $acct_name["data"]["dateOfBirth"],
                    'gender' => $acct_name["data"]["gender"],
                    'phonenumber' => $acct_name["phoneNumber"],
                    'base64Image' => $acct_name["base64Image"],
                    'responseMessage' => 'successful',
                    'responseCode' => 200
                ], 200);
                
            } else {
                return response()->json([
                    'responseMessage' => 'Something went wrong',
                    'responseCode' => 500
                ], 500);
            }
        } else {
            return response()->json([
                'responseMessage' => 'Insufficient Balance',
                'responseCode' => 500
            ], 500);
        }
        
        
        return response()->json([
            'responseMessage' => 'Resource not found.',
            'responseCode' => 404
        ], 404);
    }
    
    
}
