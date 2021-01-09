<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\BatchTransfer;
use App\Models\User;
use App\Models\UserBank;
use App\Models\PosForm;
use App\Models\Settings;
use App\Models\Logo;
use App\Models\Save;
use App\Models\Bill;
use App\Models\Power;
use App\Models\CCType;
use App\Models\ApiKey;
use App\Models\CreditCard;
use App\Models\Internet;
use App\Models\SmeBundle;
use App\Models\EPin;
use App\Models\InternetBundle;
use App\Models\Decoder;
use App\Models\DecoderBundle;
use App\Models\Mifi;
use App\Models\MifiBundle;
use App\Models\Branch;
use App\Models\Loan;
use App\Models\Bank;
use App\Models\Currency;
use App\Models\Alerts;
use App\Models\Transfer;
use App\Models\Int_transfer;
use App\Models\Plans;
use App\Models\Adminbank;
use App\Models\Gateway;
use App\Models\Deposits;
use App\Models\Banktransfer;
use App\Models\Withdraw;
use App\Models\Withdrawm;
use App\Models\Merchant;
use App\Models\Profits;
use App\Models\Ticket;
use App\Models\Reply;
use App\Models\Buyer;
use App\Models\Seller;
use App\Models\Exchange;
use App\Models\Asset;
use App\Models\Chart;
use App\Models\AirtimeSwap;
use App\Models\EpinGenerator;
use App\Models\Assettransfer;
use App\Models\Exttransfer;
use App\Models\GiftCard;
use App\Models\GiftCardType;
use App\Models\GiftCardSale;
use Carbon\Carbon;
use App\Lib\Providus;
use App\Lib\SimpleXLSX;
use App\Lib\Rubies;
use Session;
use Image;
use Redirect;
use App\Http\Resources\UserResource;
use App\Http\Resources\NewUserResource;
use App\Http\Resources\AlertCollectionResource;
use App\Events\ApiSystemLogEvent;



class UserController extends Controller
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
        $user = User::find($this->authorized_user);
        
        if ($user) {
            event(new ApiSystemLogEvent("Fetch user details", $this->authorized_user));
            return UserResource::make($user);
        }

        return response()->json([
            'responseMessage' => 'Resource not found.',
            'responseCode' => 404
        ], 404);
        
    }
    
    public function nuban(Request $request)
    {
        $user = User::where('email', $request->input('userEmail'))->first();
        
        if ($user) {
            event(new ApiSystemLogEvent("Fetch account details", $this->authorized_user));
            return response()->json([
                'accountNumber' => $user->acct_no,
                'accountName' => $user->name,
                'bankName' => 'Rubies Microfinance Bank',
                'responseMessage' => 'User NUBAN Retrieved',
                'responseCode' => 200
            ], 200);
            
        }

        return response()->json([
            'responseMessage' => 'Resource not found.',
            'responseCode' => 404
        ], 404);
        
    }
    
    public function balance()
    {
        $user = User::find($this->authorized_user);
        
        if ($user) {
            event(new ApiSystemLogEvent("Fetch balance details", $this->authorized_user));
            return response()->json([
                'balance' => $user->balance,
                'currency' => 'NGN',
                'responseMessage' => 'Balance Retrieved successfully',
                'responseCode' => 200
            ], 200);
        }

        return response()->json([
            'responseMessage' => 'Resource not found.',
            'responseCode' => 404
        ], 404);
        
    }
    
    public function pin(Request $request)
    {
        $user = User::where('email', $request->input('email'))->first();
        
        if ($user) {
            event(new ApiSystemLogEvent("changed pin", $this->authorized_user));
            $user->pin = $request->input('newPin');
            $user->save();
            return response()->json([
                'responseMessage' => 'Transaction pin reset successfully',
                'responseCode' => 200
            ], 200);
        }

        return response()->json([
            'responseMessage' => 'Resource not found',
            'responseCode' => 404
        ], 404);
        
    }
    
    public function createUser(Request $request)
    {
        
        $validator = Validator::make($request->all(), 
            [ 
                'fullName' => 'required|string|max:255',
                'userName' => 'required|min:5|unique:users|regex:/^\S*$/u',
                'email' => 'required|string|email|max:255|unique:users',
                'phoneNumber' => 'required|numeric|min:8|unique:users',
            ]
        );   
 
        if ($validator->fails()) {    
            
            return response()->json(['responseCode' => 401, 'errorMessage'=>$validator->errors()], 401);                        
        }
        
        $user = User::where('email', $request->input('email'))->first();
            
        if($user){
            
            return response()->json([
                'responseMessage' => 'User existed before',
                'responseCode' => 401
            ], 401);
                
        } else {
            
            $basic = Settings::first();

            if ($basic->email_verification == 1) {
                $email_verify = 0;
            } else {
                $email_verify = 1;
            }
    
            if ($basic->sms_verification == 1) {
                $phone_verify = 0;
            } else {
                $phone_verify = 1;
            }
            $verification_code = strtoupper(Str::random(6));
            $sms_code = strtoupper(Str::random(6));
            $email_time = Carbon::parse()->addMinutes(5);
            $phone_time = Carbon::parse()->addMinutes(5);
            
            $codeRef = str_shuffle(strtolower($request->username))."".date("his");
            
            
            $generateAcct = $this->providus->generateAccount($codeRef,$request->input('fullName'), $request->input('email'));
            
            if($generateAcct["requestSuccessful"] && $generateAcct["responseBody"]["status"] == "ACTIVE"){
                
                $acct = $generateAcct["responseBody"]["accountNumber"];
                
            } else {
                
                return response()->json([
                    'responseMessage' => 'Something went wrong, try again',
                    'responseCode' => 401
                ], 401);
                
            }
            
            $user = new User();
            $user->name = $request->input('fullName');
            $user->email = $request->input('email');
            $user->phone = $request->input('phoneNumber');
            $user->username = $request->input('userName');
            $user->email_verify = $email_verify;
            $user->verification_code = $verification_code;
            $user->sms_code = $sms_code;
            $user->email_time = $email_time;
            $user->phone_verify = $phone_verify;
            $user->phone_time = $phone_time;
            $user->balance = $basic->balance_reg;
            $user->ip_address = user_ip();
            $user->acct_no = $acct;
            $user->acct_ref = $codeRef;
            $user->pin = '0000';
            $user->password = Hash::make('12345678');
            $user->save();
            
            return NewUserResource::make($user);
            
        }
        
        return response()->json([
            'responseMessage' => 'Resource not found',
            'responseCode' => 404
        ], 404);
    }
    
    public function password(Request $request)
    {
        
        $user = User::where('email', $request->input('email'))->first();
            
        if($user){
                
            $password = Hash::make($request->input('newPassword'));
            $user->password = $password;
            $user->save();
            event(new ApiSystemLogEvent("changed password", $this->authorized_user));
            return response()->json([
                'responseMessage' => 'Password reset successfully',
                'responseCode' => 200
            ], 200);
                
        }
        
        return response()->json([
            'responseMessage' => 'Resource not found',
            'responseCode' => 404
        ], 404);
    }
    
    
    public function alerts(Request $request)
    {
        $account = $request->query('accountNumber');
        
        $user_id = User::where('acct_no', $account)->first()->id;
        
        $alert = Alerts::orderby('created_at', 'DESC')->where('user_id', $user_id)->get();
        
        if ($alert) {
            event(new ApiSystemLogEvent("Fetch all alert transaction", $this->authorized_user));
            return AlertCollectionResource::collection($alert);
        }

        return response()->json([
            'responseMessage' => 'Resource not found.',
            'responseCode' => 404
        ], 404);
        
    }
    
    
}
