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
use App\Http\Resources\InternetCollectionResource;
use App\Http\Resources\DataPlanCollectionResource;
use App\Http\Resources\CableResource;
use App\Http\Resources\PowerResource;
use App\Http\Resources\CablePlanResource;
use App\Events\ApiSystemLogEvent;


class VoucherController extends Controller
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
    
    public function airtimeProvider(Request $request){
        
        $internet = Internet::where('status', 1)->get();
        
        if ($internet) {
            event(new ApiSystemLogEvent("Fetch all airtime provider", $this->authorized_user));
            return InternetCollectionResource::collection($internet)->additional([
                'responseMessage' => 'success',
                'responseCode' => 200,
            ]);
            
        }

        return response()->json([
            'responseMessage' => 'Resource not found.',
            'responseCode' => 404
        ], 404);
        
    }
    
    public function powerProvider(Request $request){
        
        $powers = Power::get();
        
        
        if ($powers) {
            event(new ApiSystemLogEvent("Fetch all power provider", $this->authorized_user));
            return PowerResource::collection($powers)->additional([
                'responseMessage' => 'success',
                'responseCode' => 200,
            ]);
            
        }

        return response()->json([
            'responseMessage' => 'Resource not found.',
            'responseCode' => 404
        ], 404);
        
    }
    
    
    public function cableProvider(Request $request){
        
        $decoders = Decoder::get();
        
        
        if ($decoders) {
            event(new ApiSystemLogEvent("Fetch all cable provider", $this->authorized_user));
            return CableResource::collection($decoders)->additional([
                'responseMessage' => 'success',
                'responseCode' => 200,
            ]);
            
        }

        return response()->json([
            'responseMessage' => 'Resource not found.',
            'responseCode' => 404
        ], 404);
        
    }
    
    public function cablePlan(Request $request){
        
        $decoders = DecoderBundle::get();
        
        
        if ($decoders) {
            event(new ApiSystemLogEvent("Fetch all cable provider plan", $this->authorized_user));
            return CablePlanResource::collection($decoders)->additional([
                'responseMessage' => 'success',
                'responseCode' => 200,
            ]);
            
        }

        return response()->json([
            'responseMessage' => 'Resource not found.',
            'responseCode' => 404
        ], 404);
        
    }
    
    
    public function airtimeSwapProvider(Request $request){
        
        $internet = Internet::where('status', 1)->where('swap', 1)->get();
        
        if ($internet) {
            event(new ApiSystemLogEvent("Fetch all airtime provider", $this->authorized_user));
            return InternetCollectionResource::collection($internet)->additional([
                'responseMessage' => 'success',
                'responseCode' => 200,
            ]);
            
        }

        return response()->json([
            'responseMessage' => 'Resource not found.',
            'responseCode' => 404
        ], 404);
        
    }
    
    public function dataPlan(Request $request){
        
        $internet = InternetBundle::where('status', 1)->get();
        
        if ($internet) {
            event(new ApiSystemLogEvent("Fetch all data provider", $this->authorized_user));
            return DataPlanCollectionResource::collection($internet)->additional([
                'responseMessage' => 'success',
                'responseCode' => 200,
            ]);
            
        }

        return response()->json([
            'responseMessage' => 'Resource not found.',
            'responseCode' => 404
        ], 404);
        
    }

        
    public function airtimeBuy(Request $request)
    {
        
        //$request->input('amount')
        $set= Settings::first();
        $api_key = $set->clubkonnect_api_key;
        $api_id = $set->clubkonnect_user_id;
        
        
        $user=$data['user']=User::find($this->authorized_user);
        
        if($user->balance>$request->input('amount') || $user->balance==$request->input('amount')){
                
            $baseUrl = "https://www.nellobytesystems.com";
            $endpoint = "/APIAirtimeV1.asp?UserID=".$api_id."&APIKey=".$api_key."&MobileNetwork=".$request->input('provider')."&Amount=".$request->input('amount')."&MobileNumber=".$request->input('phoneNumber')."&CallBackURL=#";
            $httpVerb = "GET";
            $contentType = "application/json"; //e.g charset=utf-8
            $headers = array (
                "Content-Type: $contentType",
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_URL, $baseUrl.$endpoint);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $content = json_decode(curl_exec( $ch ),true);
            $err     = curl_errno( $ch );
            $errmsg  = curl_error( $ch );
        	curl_close($ch);
            	
        	$status = $content['status'];
            	
        	$trx = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890') , 0 , 10 );
            	
            if($status == "ORDER_RECEIVED"){
                    
                $orderid = $content['orderid'];
                $statuscode = $content['statuscode'];
            	
        	    $trx = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890') , 0 , 10 );
                
                $b = $user->balance - $request->input('amount');
                $user->balance = $b;
                $user->save();
                    
                $bills = new Bill();
                    
                $bills->status = $statuscode;
                $bills->user_id = $this->authorized_user;
                $bills->type = "Airtime Top Up";
                $bills->amount = $request->input('amount');
                $bills->orderid = $orderid;
                $bills->phone = $request->input('phoneNumber');
                $bills->trx = $trx;
                    
                $bills->save();
                    
                $token = round(microtime(true));
                $content='Acct:'.$user->acct_no.', Date:'.Carbon::now().', DR Amt:'.$request->input('amount').',
                    Bal:'.$user->balance.', Ref:'.$token.', Desc: Airtime TopUp';
                $debit['user_id']=$this->authorized_user;
                $debit['amount']=$request->input('amount');
                $debit['details']=$content;
                $debit['type']=1;
                $debit['seen']=0;
                $debit['status']=1;
                $debit['reference']=$token;
                Alerts::create($debit);
                    
                if($set->sms_notify==1){
                    send_sms($user->phone, $content);
                }
                
                $message = "Your airtime vtu transaction has been successfully, ".$request->input('phoneNumber')." has been creditted with ".$request->input('amount')."";
                $user = User::find($this->authorized_user);
                
                send_alert_email($user->email, $user->username, 'Airtime TopUp', 'DR', $user->acct_no, $token, $request->input('amount'), 'Airtime TopUp', $user->balance, Carbon::now());
                
                return response()->json([
                    'responseMessage' => 'Airtime purchased successfully',
                    'responseCode' => 200
                ], 200); 
                    
                
                    
            } else {
                    
                return response()->json([
                    'responseMessage' => 'Something went wrong, try again',
                    'responseCode' => 500
                ], 500);
                    
            }
                        
        } else {
                
                return response()->json([
                    'responseMessage' => 'Insufficient balance',
                    'responseCode' => 200
                ], 200);
                
        }

        return response()->json([
            'responseMessage' => 'Resource not found',
            'responseCode' => 404
        ], 404);
        
    }
    
    public function airtimeBuyRubies(Request $request)
    {
        
        //$request->input('amount')
        $set= Settings::first();
        
        $user=$data['user']=User::find($this->authorized_user);
        
        if($user->balance>$request->input('amount') || $user->balance==$request->input('amount')){

            $token = round(microtime(true));
            $status = $this->rubies->airtimePurchase(
                $request->input('phoneNumber'),
                $request->input('amount'),
                $request->input('providerShortCode'),
                $token);
            	
            if($status["responsecode"] == "00"){
                    
                $orderid = $status['cbareference'];
                $statuscode = $status['responsecode'];
                
                $new = $request->input('amount') - (($request->input('amount') * 3) / 100);
                
                $b = $user->balance - $new;
                $user->balance = $b;
                $user->save();
                    
                $bills = new Bill();
                    
                $bills->status = $statuscode;
                $bills->user_id = $this->authorized_user;
                $bills->type = "Airtime Top Up";
                $bills->amount = $request->input('amount');
                $bills->orderid = $orderid;
                $bills->phone = $request->input('phoneNumber');
                $bills->trx = $token;
                    
                $bills->save();
                    
                $content='Acct:'.$user->acct_no.', Date:'.Carbon::now().', DR Amt:'.$request->input('amount').',
                    Bal:'.$user->balance.', Ref:'.$token.', Desc: Airtime TopUp';
                $debit['user_id']=$this->authorized_user;
                $debit['amount']=$request->input('amount');
                $debit['details']=$content;
                $debit['type']=1;
                $debit['seen']=0;
                $debit['status']=1;
                $debit['reference']=$token;
                Alerts::create($debit);
                event(new ApiSystemLogEvent("Purchased airtime", $this->authorized_user));
                if($set->sms_notify==1){
                    send_sms($user->phone, $content);
                }
                
                $message = "Your airtime vtu transaction has been successfully, ".$request->input('phoneNumber')." has been creditted with ".$request->input('amount')."";
                $user = User::find($this->authorized_user);
                
                send_alert_email($user->email, $user->username, 'Airtime TopUp', 'DR', $user->acct_no, $token, $request->input('amount'), 'Airtime TopUp', $user->balance, Carbon::now());
                
                return response()->json([
                    'reference' => $token,
                    'responseMessage' => 'Airtime purchased successfully',
                    'responseCode' => 200
                ], 200); 
                    
            } elseif($status["responsecode"] == "-1") {
                
                //$orderid = $status['cbareference'];
                //$statuscode = $status['responsecode'];
                
                $new = $request->input('amount') - (($request->input('amount') * 3) / 100);
                
                $b = $user->balance - $new;
                $user->balance = $b;
                $user->save();
                    
                $bills = new Bill();
                    
                //$bills->status = $statuscode;
                $bills->user_id = $this->authorized_user;
                $bills->type = "Airtime Top Up";
                $bills->amount = $request->input('amount');
                //$bills->orderid = $token;
                $bills->phone = $request->input('phoneNumber');
                $bills->trx = $token;
                    
                $bills->save();
                    
                $content='Acct:'.$user->acct_no.', Date:'.Carbon::now().', DR Amt:'.$request->input('amount').',
                    Bal:'.$user->balance.', Ref:'.$token.', Desc: Airtime TopUp';
                $debit['user_id']=$this->authorized_user;
                $debit['amount']=$request->input('amount');
                $debit['details']=$content;
                $debit['type']=1;
                $debit['seen']=0;
                $debit['status']=1;
                $debit['reference']=$token;
                Alerts::create($debit);
                    
                if($set->sms_notify==1){
                    send_sms($user->phone, $content);
                }
                
                $message = "Your airtime vtu transaction has been successfully, ".$request->input('phoneNumber')." has been creditted with ".$request->input('amount')."";
                $user = User::find($this->authorized_user);
                
                send_alert_email($user->email, $user->username, 'Airtime TopUp', 'DR', $user->acct_no, $token, $request->input('amount'), 'Airtime TopUp', $user->balance, Carbon::now());
                
                return response()->json([
                    'reference' => $token,
                    'responseMessage' => $status['responsemessage'],
                    'responseCode' => 200
                ], 200); 
                
            } else {
                    
                return response()->json([
                    'responseMessage' => $status['responsemessage'],
                    'responseCode' => 500
                ], 500);
                    
            }
                        
        } else {
                
                return response()->json([
                    'responseMessage' => 'Insufficient balance',
                    'responseCode' => 500
                ], 500);
                
        }

        return response()->json([
            'responseMessage' => 'Resource not found',
            'responseCode' => 404
        ], 404);
        
    }
    
    
    public function airtimeQueries(Request $request)
    {
        
        //$request->input('amount')
        $set= Settings::first();
        
        $user=$data['user']=User::find($this->authorized_user);
        
        $status = $this->rubies->airtimeQuery($request->input('reference'));
            	
        if($status["responsecode"] == "00"){
                    
            $orderid = $status['cbareference'];
            $statuscode = $status['responsecode'];
                
                    
            $bills = Bill::where('trx', $request->input('reference'))->first();
                    
            $bills->status = $statuscode;
            $bills->orderid = $orderid;
            
            $bills->save();
                    
            event(new ApiSystemLogEvent("query airtime transaction", $this->authorized_user));
            return response()->json([
                'reference' => $request->input('reference'),
                'responseMessage' => 'Airtime purchased successfully',
                'responseCode' => 200
            ], 200); 
                    
        } else {
                
            return response()->json([
                'responseMessage' => $status['responsemessage'],
                'responseCode' => 500
            ], 500);
                
        }

        return response()->json([
            'responseMessage' => 'Resource not found',
            'responseCode' => 404
        ], 404);
        
    }
    
    public function dataBuy(Request $request)
    {
        
        $network = $request->input('provider');
        $phone = $request->input('phoneNumber');
        $plan = $request->input('plan');
        $amount = InternetBundle::where('code', $network)->where('plan', $plan)->first()->cost;
        
        $set= Settings::first();
        $api_key = $set->clubkonnect_api_key;
        $api_id = $set->clubkonnect_user_id;
        
        
        $user=$data['user']=User::find($this->authorized_user);
        
        if($user->balance>$amount || $user->balance==$amount){
                
            $baseUrl = "https://www.nellobytesystems.com";
            $endpoint = "/APIDatabundleV1.asp?UserID=".$api_id."&APIKey=".$api_key."&MobileNetwork=".$network."&DataPlan=".$plan."&MobileNumber=".$phone."&CallBackURL=#";
            $httpVerb = "GET";
            $contentType = "application/json"; //e.g charset=utf-8
            $headers = array (
                "Content-Type: $contentType",
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_URL, $baseUrl.$endpoint);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $content = json_decode(curl_exec( $ch ),true);
            $err     = curl_errno( $ch );
            $errmsg  = curl_error( $ch );
        	curl_close($ch);
            	
            	
        	$status = $content['status'];
            	
        	$trx = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890') , 0 , 10 );
        	
            if($status == "ORDER_RECEIVED"){
                    
                $orderid = $content['orderid'];
                $statuscode = $content['statuscode'];
        	
        	    $trx = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890') , 0 , 10 );
                
                $b = $user->balance - $amount;
                $user->balance = $b;
                $user->save();
                
                $bills = new Bill();
                
                $bills->status = $statuscode;
                $bills->user_id = $this->authorized_user;
                $bills->type = "Data Bundle";
                $bills->amount = $amount;
                $bills->orderid = $orderid;
                $bills->phone = $phone;
                $bills->trx = $trx;
                    
                $bills->save();
                    
                $token = round(microtime(true));
                $content='Acct:'.$user->acct_no.', Date:'.Carbon::now().', DR Amt:'.$request->amount.',
                    Bal:'.$user->balance.', Ref:'.$token.', Desc: Data Bundle Topup';
                $debit['user_id']=$this->authorized_user;
                $debit['amount']=$amount;
                $debit['details']=$content;
                $debit['type']=1;
                $debit['seen']=0;
                $debit['status']=1;
                $debit['reference']=$token;
                Alerts::create($debit);
                    
                if($set->sms_notify==1){
                    send_sms($user->phone, $content);
                }
                
                $message = "Your data bundle transaction has been successfully, ".$phone." has been creditted with ".$plan."MB";
                $user = User::find($this->authorized_user);
                    
                send_alert_email($user->email, $user->username, 'Data Bundle TopUp Successfully', 'DR', $user->acct_no, $token, $amount, 'Data Bundle TopUp', $user->balance, Carbon::now());
                    
                    
                return response()->json([
                    'responseMessage' => 'Data purchased successfully',
                    'responseCode' => 200
                ], 200);
                    
            } else {
                    
                return response()->json([
                    'responseMessage' => 'Something went wrong, try again',
                    'responseCode' => 500
                ], 500);
                    
            }
                        
        } else {
                
            return response()->json([
                'responseMessage' => 'Insufficient balance',
                'responseCode' => 200
            ], 200);
                
        }

        
        return response()->json([
            'responseMessage' => 'Resource not found',
            'responseCode' => 404
        ], 404);
    }
    
    
    public function airtimeSwap(Request $request){
        
        $network = Internet::where('code', $request->input('provider'))->first();
        $number = AirtimeSwap::where('user_id', $this->authorized_user)->where('status', '0')->get()->count();
        
        if($number<1){
            
            if($network->swap == 1){
                
                $airtimeswap = new AirtimeSwap();
                $airtimeswap->user_id = $this->authorized_user;
                $airtimeswap->status = "0";
                $airtimeswap->amount = $request->input('amount');
                $airtimeswap->amount_to_receive = $request->input('amount');
                $airtimeswap->phone = $request->input('phoneNumber');
                
                if($airtimeswap->save()){
                    event(new ApiSystemLogEvent("airtime swap successful", $this->authorized_user));
                    $user = User::find($this->authorized_user);
                    send_email($user->email, $user->username, 'Airtime Swap', 'Your are to send #'.$request->input('amount').' to this number '.$network->number);
                    
                    return response()->json([
                        'responseMessage' => 'Your are to send #'.$request->input('amount').' to this number '.$network->number,
                        'responseCode' => 200
                    ], 200);
                }
                
            } else {
                
                 
                return response()->json([
                    'responseMessage' => 'Sorry! Airtime Swap is not available for this network',
                    'responseCode' => 500
                ], 500);
            }
            
        } else {
            
            return response()->json([
                'responseMessage' => 'Sorry!, Please complete your last transaction on AirtimeSwap',
                'responseCode' => 500
            ], 500);
        }
        
        return response()->json([
            'responseMessage' => 'Resource not found',
            'responseCode' => 404
        ], 404);
        
    }
    
    public function powerBuy(Request $request)
    {
        
        $company = $request->input('powerCode');
        $meterno = $request->input('meterNumber');
        $package = $request->input('meterType');
        $amount = $request->input('amount');
        
        $set= Settings::first();
        $api_key = $set->clubkonnect_api_key;
        $api_id = $set->clubkonnect_user_id;
        
        
        $user=$data['user']=User::find($this->authorized_user);
        
        if($user->balance>$amount || $user->balance==$amount){
                
            $baseUrl = "https://www.nellobytesystems.com";
            $endpoint = "/APIElectricityV1.asp?UserID=".$api_id."&APIKey=".$api_key."&ElectricCompany=".$company."&MeterType=".$package."&MeterNo=".$meterno."&Amount=".$amount."&CallBackURL=#";
                
            $httpVerb = "GET";
            $contentType = "application/json"; //e.g charset=utf-8
            $headers = array (
                "Content-Type: $contentType",
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_URL, $baseUrl.$endpoint);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $content = json_decode(curl_exec( $ch ),true);
            $err     = curl_errno( $ch );
            $errmsg  = curl_error( $ch );
            curl_close($ch);
            	
            $status = $content['status'];
            	
            $trx = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890') , 0 , 10 );
            	
            if($status == "ORDER_RECEIVED"){
                    
                $orderid = $content['orderid'];
                $statuscode = $content['statuscode'];
            
                $trx = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890') , 0 , 10 );
                    
                $b = $user->balance - $amount;
                $user->balance = $b;
                $user->save();
                    
                $bills = new Bill();
                    
                $bills->status = $statuscode;
                $bills->user_id = $this->authorized_user;
                $bills->type = "Electricity Bill Payment";
                $bills->amount = $amount;
                $bills->orderid = $orderid;
                $bills->phone = $phone;
                $bills->trx = $trx;
                    
                $bills->save();
                    
                $token = round(microtime(true));
                $content='Acct:'.$user->acct_no.', Date:'.Carbon::now().', DR Amt:'.$amount.',
                    Bal:'.$user->balance.', Ref:'.$token.', Desc: Electricity Bill Payment';
                $debit['user_id']=$this->authorized_user;
                $debit['amount']=$amount;
                $debit['details']=$content;
                $debit['type']=1;
                $debit['seen']=0;
                $debit['status']=1;
                $debit['reference']=$token;
                Alerts::create($debit);
                
                if($set->sms_notify==1){
                    send_sms($user->phone, $content);
                }
                    
                $message = "Your Electricity Bill Payment transaction has been successfully, ".$smartcard." has been loaded with your choosing package";
                $user = User::find($this->authorized_user);
                    
                send_alert_email($user->email, $user->username, 'Electricity Bill Payment', 'DR', $user->acct_no, $token, $amount, 'Electricity Bill Payment', $user->balance, Carbon::now());
                    
                    
                return response()->json([
                    'responseMessage' => 'Electicity Bill Payment Successfully',
                    'responseCode' => 200
                ], 200);
                    
            } else {
                    
                return response()->json([
                    'responseMessage' => 'Something went wrong, Please try again',
                    'responseCode' => 200
                ], 200);
                    
            }
                        
        } else {
                
            return response()->json([
                'responseMessage' => 'Insufficient Balance',
                'responseCode' => 200
            ], 200);
        }
            
        
        return response()->json([
            'responseMessage' => 'Resource not found',
            'responseCode' => 404
        ], 404);
    }
    
    public function powerVerify(Request $request)
    {
        
        $company = $request->input('powerCode');
        $meterno = $request->input('meterNumber');
        
        $set= Settings::first();
        $api_key = $set->clubkonnect_api_key;
        $api_id = $set->clubkonnect_user_id;
        
        
        $user=$data['user']=User::find($this->authorized_user);
        
                
        $baseUrl = "https://www.nellobytesystems.com";
        $endpoint = "/APIVerifyElectricityV1.asp?UserID=".$api_id."&APIKey=".$api_key."&ElectricCompany=".$company."&MeterNo=".$meterno;
        
        $httpVerb = "GET";
        $contentType = "application/json"; //e.g charset=utf-8
        $headers = array (
            "Content-Type: $contentType",
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $baseUrl.$endpoint);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $content = json_decode(curl_exec( $ch ),true);
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        curl_close($ch);
            	
        $status = $content['customer_name'];
        
        if($status == 'INVALID_METERNO'){
            
            return response()->json([
                'responseMessage' => 'Invalid Meter Number',
                'responseCode' => 404
            ], 404);
            
        } else {
            
            return response()->json([
                'customerName' => $status,
                'responseMessage' => 'success',
                'responseCode' => 404
            ], 404);
            
            
        }
        
            
        
        return response()->json([
            'responseMessage' => 'Resource not found',
            'responseCode' => 404
        ], 404);
    }
    
    public function cableVerify(Request $request)
    {
        
        $company = $request->input('cableCode');
        $cardno = $request->input('cardNumber');
        
        $set= Settings::first();
        $api_key = $set->clubkonnect_api_key;
        $api_id = $set->clubkonnect_user_id;
        
        
        $user=$data['user']=User::find($this->authorized_user);
        
                
        $baseUrl = "https://www.nellobytesystems.com";
        $endpoint = "/APIVerifyCableTVV1.0.asp?UserID=".$api_id."&APIKey=".$api_key."&CableTV=".$company."&SmartCardNo=".$cardno;
        
        $httpVerb = "GET";
        $contentType = "application/json"; //e.g charset=utf-8
        $headers = array (
            "Content-Type: $contentType",
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $baseUrl.$endpoint);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $content = json_decode(curl_exec( $ch ),true);
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        curl_close($ch);
            	
        $status = $content['customer_name'];
        
        if($status == 'INVALID_SMARTCARDNO'){
            
            return response()->json([
                'responseMessage' => 'Invalid Meter Number',
                'responseCode' => 404
            ], 404);
            
        } else {
            
            return response()->json([
                'customerName' => $status,
                'responseMessage' => 'success',
                'responseCode' => 404
            ], 404);
            
            
        }
        
            
        
        return response()->json([
            'responseMessage' => 'Resource not found',
            'responseCode' => 404
        ], 404);
    }
    
    
    public function cableBuy(Request $request)
    {
        
        $decoder = $request->input('cableCode');
        $smartcard = $request->input('cardNumber');
        $package = $request->input('planCode');
        $amount = DecoderBundle::where('code', $decoder)->where('plan', $package)->first();
        
        $set= Settings::first();
        $api_key = $set->clubkonnect_api_key;
        $api_id = $set->clubkonnect_user_id;
        
        
        $user=$data['user']=User::find($this->authorized_user);
        
        if(!empty($decoder) && !empty($package) && !empty($smartcard)){
            if($user->balance>$amount || $user->balance==$amount){
                
                $baseUrl = "https://www.nellobytesystems.com";
                $endpoint = "/APICableTVV1.asp?UserID=".$api_id."&APIKey=".$api_key."&CableTV=".$decoder."&Package=".$package."&SmartCardNo=".$smartcard."&CallBackURL=#";
                
                $httpVerb = "GET";
                $contentType = "application/json"; //e.g charset=utf-8
                $headers = array (
                    "Content-Type: $contentType",
                );
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_URL, $baseUrl.$endpoint);
                curl_setopt($ch, CURLOPT_HTTPGET, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $content = json_decode(curl_exec( $ch ),true);
                $err     = curl_errno( $ch );
                $errmsg  = curl_error( $ch );
            	curl_close($ch);
            	
            	$status = $content['status'];
            	
            	$trx = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890') , 0 , 10 );
            	
                if($status == "ORDER_RECEIVED"){
                    
                    $orderid = $content['orderid'];
                    $statuscode = $content['statuscode'];
            	
            	    $trx = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890') , 0 , 10 );
                    
                    $b = $user->balance - $amount;
                    $user->balance = $b;
                    $user->save();
                    
                    $bills = new Bill();
                    
                    $bills->status = $statuscode;
                    $bills->user_id = $this->authorized_user;
                    $bills->type = "CableTv Subscription";
                    $bills->amount = $amount;
                    $bills->orderid = $orderid;
                    $bills->phone = $phone;
                    $bills->trx = $trx;
                    
                    $bills->save();
                    
                    $token = round(microtime(true));
                    $content='Acct:'.$user->acct_no.', Date:'.Carbon::now().', DR Amt:'.$amount.',
                        Bal:'.$user->balance.', Ref:'.$token.', Desc: CableTv Subscription';
                    $debit['user_id']=$this->authorized_user;
                    $debit['amount']=$amount;
                    $debit['details']=$content;
                    $debit['type']=1;
                    $debit['seen']=0;
                    $debit['status']=1;
                    $debit['reference']=$token;
                    Alerts::create($debit);
                    
                    if($set->sms_notify==1){
                        send_sms($user->phone, $content);
                    }
                    
                    $message = "Your CableTv Subscription transaction has been successfully, ".$smartcard." has been loaded with your choosing package";
                    $user = User::find($this->authorized_user);
                    
                    send_alert_email($user->email, $user->username, 'Cable Subscription', 'DR', $user->acct_no, $token, $amount, 'Cable Subscription', $user->balance, Carbon::now());
                    
                    
                   return response()->json([
                        'responseMessage' => 'CableTv Subscription Successfully',
                        'responseCode' => 200
                    ], 200);
                    
                } else {
                    
                    return response()->json([
                        'responseMessage' => 'Something went wrong, Please try again',
                        'responseCode' => 200
                    ], 200);
                    
                }
                        
            } else {
                
                return response()->json([
                    'responseMessage' => 'Insufficient Balance',
                    'responseCode' => 200
                ], 200);
                
            }
            
        } else {
            
            return response()->json([
                'responseMessage' => 'expected parameter are empty',
                'responseCode' => 200
            ], 200);
            
        }  
        
        
        return response()->json([
            'responseMessage' => 'Resource not found',
            'responseCode' => 404
        ], 404);
    }
    
    
}
