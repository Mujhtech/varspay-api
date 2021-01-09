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
use App\Models\VirtualAccount;
use App\Models\VirtualAccountTransaction;
use Session;
use Image;
use Redirect;
use App\Http\Resources\VirtualAccountResource;
use App\Http\Resources\VirtualAlertCollectionResource;

use App\Events\ApiSystemLogEvent;


class VirtualAccountController extends Controller
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
        $user = VirtualAccount::where('user_id', $this->authorized_user)->where('status', 1)->get();
        
        if ($user) {
            event(new ApiSystemLogEvent("Fetch all virtual account", $this->authorized_user));
            return VirtualAccountResource::collection($user)->additional([
                'responseMessage' => 'Retrived all virtual account',
                'responseCode' => 200,
            ]);
        }

        return response()->json([
            'responseMessage' => 'Resource not found.',
            'responseCode' => 404
        ], 404);
        
    }
    
    public function createAccount(Request $request)
    {
        
        if(VirtualAccount::where('user_id', $this->authorized_user)->where('status', 1)->get()->count() <= 3000){
            $user = $this->rubies->generateVirtualAccount($request->input('name'));
            
            if($user["responsecode"] == "00" && $user["responsemessage"] == "ACCOUNT OPENED SUCCESSFULLY"){
                
                $v_acct = new VirtualAccount;
                $v_acct->acct_no = $user['virtualaccount'];
                $v_acct->acct_name = $user['virtualaccountname'];
                $v_acct->bankcode = $user['bankcode'];
                $v_acct->user_id = $this->authorized_user;
                $v_acct->status = 1;
            
                if ($v_acct->save()) {
                    event(new ApiSystemLogEvent("Create virtual account", $this->authorized_user));
                    return response()->json([
                        'accountNumber' => $user['virtualaccount'],
                        'accountName' => $user['virtualaccountname'],
                        'bankName' => 'Rubies Microfinance Bank',
                        'responseMessage' => 'Virtual Account Created',
                        'responseCode' => 200
                    ], 200);
                    
                } else {
                    
                    return response()->json([
                        'responseMessage' => 'Something went wrong',
                        'responseCode' => 500
                    ], 500);
                    
                }
                
            }
        } else {
            return response()->json([
                'responseMessage' => 'You have reached maximum number of virtual account, Please contact our technical support for more information',
                'responseCode' => 500
            ], 500);
        }

        return response()->json([
            'responseMessage' => 'Resource not found.',
            'responseCode' => 404
        ], 404);
        
    }
    


    
    
    public function listAllTran(Request $request)
    {
        $account = $request->input('virtualaccount');
        
        $alert = VirtualAccountTransaction::orderby('created_at', 'DESC')->where('acct_no', $account)->get();
        
        if ($alert) {
            event(new ApiSystemLogEvent("Get all transaction on virtual account", $this->authorized_user));
            return VirtualAlertCollectionResource::collection($alert)->additional([
                'responseMessage' => 'successful',
                'responseCode' => 200,
            ]);;
        }

        return response()->json([
            'responseMessage' => 'Resource not found.',
            'responseCode' => 404
        ], 404);
        
    }
    
    
}
