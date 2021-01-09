<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Events\SystemLogEvent;
use App\Models\CryptoCurrency;
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
use App\Models\BatchTransferList;
use App\Models\CardlessWithdraw;
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




class UserController extends Controller
{
    
    public $providus;
    
    public $rubies;

        
    public function __construct()
    {		
        $this->middleware('auth');
        $this->providus = new Providus;
        $this->rubies = new Rubies;
    }

        
    public function dashboard()
    {
        $data['title']='Dashboard';
        $data['alertx']=Alerts::where('user_id',Auth::user()->id)->orderBy('id', 'DESC')->limit(1)->get();
        $data['asset']=Asset::where('user_id', Auth::user()->id)->get();
        $data['plan']=Chart::whereStatus(1)->get();
        event(new SystemLogEvent("Checked in to dashboard", Auth::user()->id));
        return view('user.index', $data);
    }
        
    public function save()
    {
		$data['title']='Save 4 Me';
        $data['save']=Save::whereUser_id(Auth::user()->id)->get();
        event(new SystemLogEvent("Checked in to save", Auth::user()->id));
        return view('user.save', $data);
    } 
        
    public function branch()
    {
        $data['title']='Bank branches';
        $data['branch']=Branch::all();
        event(new SystemLogEvent("Checked in to branch", Auth::user()->id));
        return view('user.branch', $data);
    } 
    
    public function merchant()
    {
        $data['title']='Merchant';
        $data['merchant']=Merchant::whereUser_id(Auth::user()->id)->get();
        event(new SystemLogEvent("Checked in to merchant", Auth::user()->id));
        return view('user.merchant', $data);
    }    
    
    public function ticket()
    {
        $data['title']='Tickets';
        $data['ticket']=Ticket::whereUser_id(Auth::user()->id)->get();
        event(new SystemLogEvent("Checked in to ticket", Auth::user()->id));
        return view('user.ticket', $data);
    }     
    
    public function senderlog()
    {
        $data['title']='Sender log';
        $data['sent']=Exttransfer::whereUser_id(Auth::user()->id)->get();
        event(new SystemLogEvent("Checked in to senderlog", Auth::user()->id));
        return view('user.sender-log', $data);
    } 
        
    public function loan()
    {
        $data['title']='Loan management';
        $data['loan'] = Loan::whereUser_id(Auth::user()->id)->get();
        $data['bank'] = UserBank::where('user_id', Auth::user()->id)->first();
        event(new SystemLogEvent("Checked in to loan", Auth::user()->id));
        return view('user.loan', $data);
    } 
        
    public function statement()
    {
        $data['title']='Transaction history';
        event(new SystemLogEvent("Checked in to statement", Auth::user()->id));
        return view('user.statement', $data);
    } 
    
    public function addmerchant()
    {
        $data['title']='Add merchant';
        event(new SystemLogEvent("Checked in to add merchant", Auth::user()->id));
        return view('user.add-merchant', $data);
    }     
    
    public function merchant_documentation()
    {
        $data['title']='Documentation';
        event(new SystemLogEvent("Checked in to merchant documentation", Auth::user()->id));
        return view('user.merchant-documentation', $data);
    } 
    
    public function airtimeTop()
    {
        $data['title']='Airtime Topup';
        $data['network'] = Internet::wherePhone(1)->latest()->get();
        event(new SystemLogEvent("Checked in to airtime top up", Auth::user()->id));
        return view('user.airtimetopup', $data);
    }
    
    public function atmcard()
    {
        $data['title']= 'ATM Cards';
        event(new SystemLogEvent("Checked in to atm cards", Auth::user()->id));
        $data['cctype'] = CCType::all();
        $data['creditcard'] = CreditCard::where('user_id', Auth::user()->id)->get();
        return view('user.atmcard', $data);
    }
    
    
    public function airtimeSwap()
    {
        $data['title']='Airtime Swap';
        $data['network'] = Internet::whereSwap(1)->latest()->get();
        event(new SystemLogEvent("Checked in to airtime swap", Auth::user()->id));
        return view('user.airtimeswap', $data);
    }
    
    public function power()
    {
        $data['title']='Electricity Bill Payment';
        event(new SystemLogEvent("Checked in to electricity bill payment", Auth::user()->id));
        $data['power'] = Power::whereStatus(1)->latest()->get();
        return view('user.power', $data);
    }
    
    public function dataBundle()
    {
        $data['title']='Data Bundle';
        event(new SystemLogEvent("Checked in to data bundle", Auth::user()->id));
        $data['network'] = Internet::whereStatus(1)->latest()->get();
        return view('user.databundle', $data);
    }
    
    public function smeData()
    {
        $data['title']='SME Data Bundle';
        $data['network'] = Internet::whereStatus(1)->latest()->get();
        event(new SystemLogEvent("Checked in to sme data", Auth::user()->id));
        return view('user.smedatabundle', $data);
    }
    
    public function tvSub()
    {
        $data['title']='Cable TV Subscription';
        $data['decoder'] = Decoder::whereStatus(1)->latest()->get();
        event(new SystemLogEvent("Checked in to cable tv subscription", Auth::user()->id));
        return view('user.tvsub', $data);
    }
    
    public function internet()
    {
        $data['title']='Internet Bundle';
        $data['mifi'] = Mifi::whereStatus(1)->latest()->get();
        event(new SystemLogEvent("Checked in to internet bundle", Auth::user()->id));
        return view('user.internet', $data);
    }
    
    public function ePin()
    {
        $data['title']='E-Pin';
        $data['epin'] = EPin::whereStatus(1)->latest()->get();
        event(new SystemLogEvent("Checked in to e-pin", Auth::user()->id));
        return view('user.epin', $data);
    }
    
    public function myePin()
    {
        $data['title']='My E-Pin';
        $data['epin'] = EpinGenerator::where('user_id', Auth::user()->id)->latest()->get();
        event(new SystemLogEvent("Checked in to my e-pin", Auth::user()->id));
        return view('user.myepin', $data);
    }
    
    
    public function ajaxDatabundle(Request $request){

                
        $data['plan'] = InternetBundle::select('id','name', 'plan')->where('code', $request->network_code)->where('sme', '0')->get();
        
                        
        echo json_encode($data);
    }
    
    public function ajaxSmeDatabundle(Request $request){

                
        $data['plan'] = InternetBundle::select('id','name', 'plan')->where('code', $request->network_code)->where('sme', '1')->get();
        
                        
        echo json_encode($data);
    }
    
    public function ajaxDecoder(Request $request){
        
        $data['plan'] = DecoderBundle::select('id','name', 'plan')->where('code', $request->decoder_code)->get();
        
                        
        echo json_encode($data);
        

    }
    
    public function ajaxDecoderAmount(Request $request){
        
        $data = DecoderBundle::where('id', $request->pidNum)->get();
        
        echo $data[0]->cost;
    }
    
    public function ajaxDataBundleAmount(Request $request){
        
        $data = InternetBundle::where('id', $request->pid)->get();
        
        echo $data[0]->cost;
    }
    
    public function billPayment()
    {
        $data['title']='Bill Payment';
        event(new SystemLogEvent("Checked in to bill payment", Auth::user()->id));
        return view('user.billpayment', $data);
    }
    
    public function airtimetopSubmit(Request $request)
    {
        
        $network = $request->network;
        $phone = $request->number;
        $amount = $request->amount;
        
        $set= Settings::first();
        $api_key = $set->clubkonnect_api_key;
        $api_id = $set->clubkonnect_user_id;
        
        
        $user=$data['user']=User::find(Auth::user()->id);
        
        event(new SystemLogEvent("Attempt to submit airtime top up form", Auth::user()->id));
        
        if($user->pin == $request->pin){
            if($user->balance>$amount || $user->balance==$amount){
                
                $baseUrl = "https://www.nellobytesystems.com";
                $endpoint = "/APIAirtimeV1.asp?UserID=".$api_id."&APIKey=".$api_key."&MobileNetwork=".$network."&Amount=".$amount."&MobileNumber=".$phone."&CallBackURL=#";
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
                    $bills->user_id = Auth::user()->id;
                    $bills->type = "Airtime Top Up";
                    $bills->amount = $amount;
                    $bills->orderid = $orderid;
                    $bills->phone = $phone;
                    $bills->trx = $trx;
                    
                    $bills->save();
                    
                    $token = round(microtime(true));
                    $content='Acct:'.$user->acct_no.', Date:'.Carbon::now().', DR Amt:'.$amount.',
                        Bal:'.$user->balance.', Ref:'.$token.', Desc: Airtime TopUp';
                    $debit['user_id']=Auth::user()->id;
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
                    
                    $message = "Your airtime vtu transaction has been successfully, ".$phone." has been creditted with ".$amount."";
                    $user = User::find(Auth::user()->id);
                    
                    send_alert_email($user->email, $user->username, 'Airtime TopUp', 'DR', $user->acct_no, $token, $amount, 'Airtime TopUp', $user->balance, Carbon::now());
                    
                    
                    return back()->with('success', $message);
                    
                } else {
                    
                    return back()->with('alert', 'Somethin went wrong, try again');
                    
                }
                        
            } else {
                
                return back()->with('alert', 'Account balance is insufficient');
                
            }
            
        } else {
                return back()->with('alert', 'Invalid pin.');
        }        
    }
    
    public function airtimetopSubmitRubies(Request $request)
    {
        
        $network = $request->network;
        $phone = $request->number;
        $amount = $request->amount;
        
        $set= Settings::first();
        $api_key = $set->clubkonnect_api_key;
        $api_id = $set->clubkonnect_user_id;
        
        
        $user=$data['user']=User::find(Auth::user()->id);
        event(new SystemLogEvent("Attempt to submit airtime top up form", Auth::user()->id));
        
        if($user->pin == $request->pin){
            if($user->balance>$amount || $user->balance==$amount){
                
                $token = round(microtime(true));
                event(new SystemLogEvent("Transaction created: ".$token, Auth::user()->id));
                
                event(new SystemLogEvent("Airtime Top up server processing", Auth::user()->id));
                $status = $this->rubies->airtimePurchase($phone,$amount,$network,$token);
            	
                if($status["responsecode"] == "00"){
                    
                    $orderid = $status['cbareference'];
                    $statuscode = $status['responsecode'];
                    event(new SystemLogEvent("Airtime Top Up server completed", Auth::user()->id));
            	
                    $new = $amount - (($amount * 3) / 100);
                    $b = $user->balance - $new;
                    $user->balance = $b;
                    $user->save();
                    event(new SystemLogEvent("User debited", Auth::user()->id));
                    
                    $bills = new Bill();
                    
                    $bills->status = $statuscode;
                    $bills->user_id = Auth::user()->id;
                    $bills->type = "Airtime Top Up";
                    $bills->amount = $amount;
                    $bills->orderid = $orderid;
                    $bills->phone = $phone;
                    $bills->trx = $token;
                    
                    $bills->save();
                    
                    
                    $content = 'Acct:'.$user->acct_no.', Date:'.Carbon::now().', DR Amt:'.$new.',
                        Bal:'.$user->balance.', Ref:'.$token.', Desc: Airtime TopUp';
                    $debit['user_id']=Auth::user()->id;
                    $debit['amount']=$new;
                    $debit['details']=$content;
                    $debit['type']=1;
                    $debit['seen']=0;
                    $debit['status']=1;
                    $debit['reference']=$token;
                    Alerts::create($debit);
                    
                    if($set->sms_notify==1){
                        send_sms($user->phone, $content);
                    }
                    
                    $message = "Your airtime vtu transaction has been successfully, ".$phone." has been creditted with ".$amount."";
                    $user = User::find(Auth::user()->id);
                    
                    send_alert_email($user->email, $user->username, 'Airtime TopUp', 'DR', $user->acct_no, $token, $new, 'Airtime TopUp', $user->balance, Carbon::now());
                    
                    event(new SystemLogEvent("Airtime purchased successfully", Auth::user()->id));
                    return back()->with('success', $message);
                    
                } else {
                    
                    event(new SystemLogEvent("Airtime top up server failed", Auth::user()->id));
                    return back()->with('alert', 'Something went wrong, try again');
                    
                }
                        
            } else {
                
                return back()->with('alert', 'Account balance is insufficient');
                
            }
            
        } else {
                return back()->with('alert', 'Invalid pin.');
        }        
    }
    
    public function databundleSubmit(Request $request)
    {
        
        $network = $request->network;
        $phone = $request->number;
        $amount = $request->amount;
        $plan = $request->plan;
        
        $set= Settings::first();
        $api_key = $set->clubkonnect_api_key;
        $api_id = $set->clubkonnect_user_id;
        
        
        $user=$data['user']=User::find(Auth::user()->id);
        
        if($user->pin == $request->pin){
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
                    $bills->user_id = Auth::user()->id;
                    $bills->type = "Data Bundle";
                    $bills->amount = $amount;
                    $bills->orderid = $orderid;
                    $bills->phone = $phone;
                    $bills->trx = $trx;
                    
                    $bills->save();
                    
                    $token = round(microtime(true));
                    $content='Acct:'.$user->acct_no.', Date:'.Carbon::now().', DR Amt:'.$request->amount.',
                        Bal:'.$user->balance.', Ref:'.$token.', Desc: Data Bundle Topup';
                    $debit['user_id']=Auth::user()->id;
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
                    $user = User::find(Auth::user()->id);
                    
                    send_alert_email($user->email, $user->username, 'Data Bundle TopUp Successfully', 'DR', $user->acct_no, $token, $amount, 'Data Bundle TopUp', $user->balance, Carbon::now());
                    
                    
                    
                    return back()->with('success', $message);
                    
                } else {
                    
                    return back()->with('alert', 'Somethin went wrong, try again');
                    
                }
                        
            } else {
                
                return back()->with('alert', 'Account balance is insufficient');
                
            }
            
        } else {
                return back()->with('alert', 'Invalid pin.');
        }        
    }
    
    public function tvsubSubmit(Request $request)
    {
        
        $decoder = $request->decoder;
        $smartcard = $request->number;
        $package = $request->plan;
        $amount = $request->amount;
        
        $set= Settings::first();
        $api_key = $set->clubkonnect_api_key;
        $api_id = $set->clubkonnect_user_id;
        
        
        $user=$data['user']=User::find(Auth::user()->id);
        
        if($user->pin == $request->pin){
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
                    $bills->user_id = Auth::user()->id;
                    $bills->type = "CableTv Subscription";
                    $bills->amount = $amount;
                    $bills->orderid = $orderid;
                    $bills->phone = $phone;
                    $bills->trx = $trx;
                    
                    $bills->save();
                    
                    $token = round(microtime(true));
                    $content='Acct:'.$user->acct_no.', Date:'.Carbon::now().', DR Amt:'.$amount.',
                        Bal:'.$user->balance.', Ref:'.$token.', Desc: CableTv Subscription';
                    $debit['user_id']=Auth::user()->id;
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
                    $user = User::find(Auth::user()->id);
                    
                    send_alert_email($user->email, $user->username, 'Cable Subscription', 'DR', $user->acct_no, $token, $amount, 'Cable Subscription', $user->balance, Carbon::now());
                    
                    
                    return back()->with('success', $message);
                    
                } else {
                    
                    return back()->with('alert', 'Somethin went wrong, try again');
                    
                }
                        
            } else {
                
                return back()->with('alert', 'Account balance is insufficient');
                
            }
            
        } else {
                return back()->with('alert', 'Invalid pin.');
        }        
    }
    
    public function powerSubmit(Request $request)
    {
        
        $company = $request->company;
        $meterno = $request->meternumber;
        $package = $request->plan;
        $amount = $request->amount;
        
        $set= Settings::first();
        $api_key = $set->clubkonnect_api_key;
        $api_id = $set->clubkonnect_user_id;
        
        
        $user=$data['user']=User::find(Auth::user()->id);
        
        if($user->pin == $request->pin){
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
                    $bills->user_id = Auth::user()->id;
                    $bills->type = "Electricity Bill Payment";
                    $bills->amount = $amount;
                    $bills->orderid = $orderid;
                    $bills->phone = $phone;
                    $bills->trx = $trx;
                    
                    $bills->save();
                    
                    $token = round(microtime(true));
                    $content='Acct:'.$user->acct_no.', Date:'.Carbon::now().', DR Amt:'.$amount.',
                        Bal:'.$user->balance.', Ref:'.$token.', Desc: Electricity Bill Payment';
                    $debit['user_id']=Auth::user()->id;
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
                    $user = User::find(Auth::user()->id);
                    
                    send_alert_email($user->email, $user->username, 'Electricity Bill Payment', 'DR', $user->acct_no, $token, $amount, 'Electricity Bill Payment', $user->balance, Carbon::now());
                    
                    
                    return back()->with('success', $message);
                    
                } else {
                    
                    return back()->with('alert', 'Somethin went wrong, try again');
                    
                }
                        
            } else {
                
                return back()->with('alert', 'Account balance is insufficient');
                
            }
            
        } else {
                return back()->with('alert', 'Invalid pin.');
        }        
    }
    
    public function ePinFetch(){
        
    }
    
    public function requestAtmcard(Request $request)
    {
        
        $card = $request->card;
        
        $set= Settings::first();
        $api_key = $set->clubkonnect_api_key;
        $api_id = $set->clubkonnect_user_id;
        
        
        $user=$data['user']=User::find(Auth::user()->id);
        $card = CCType::whereId($request->card)->first();
        $charge = $card->charge;
        
        if($user->pin == $request->pin){
            if($user->balance>$charge || $user->balance==$charge){
                
                $b = $user->balance - $charge;
                $user->balance = $b;
                $user->save();
                
                $trx = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890') , 0 , 6 );
                $ccv = substr(str_shuffle('01234567890') , 0 , 3 );
                $pin = substr(str_shuffle('01234567890') , 0 , 4 );
                $number = substr(str_shuffle('01234567890') , 0 , 15 );
                $dp=new CreditCard();
                $dp->charge= $charge;
                $dp->type= $card->type;
                $dp->name= $card->name;
                $dp->ccv= $ccv;
                $dp->pin= $pin;
                $dp->number= $number;
                $dp->status= 2; 
                $dp->trx_id= $trx;  
                $dp->user_id= Auth::user()->id;
                $dp->save();
                
                $token = round(microtime(true));
                $content='Acct:'.$user->acct_no.', Date:'.Carbon::now().', DR Amt:'.$charge.',
                    Bal:'.$user->balance.', Ref:'.$token.', Desc: Credit Card Payment';
                $debit['user_id']=Auth::user()->id;
                $debit['amount']=$charge;
                $debit['details']=$content;
                $debit['type']=1;
                $debit['seen']=0;
                $debit['status']=1;
                $debit['reference']=$token;
                Alerts::create($debit);
                    
                if($set->sms_notify==1){
                    send_sms($user->phone, $content);
                }
                    
                $message = "New card requested successfully. #".$card->charge." has been deducted from your account balance. You will recieve a mail once your new card is available for pickup";
                    
                send_alert_email($user->email, $user->username, 'Credit Card Payment', 'DR', $user->acct_no, $token, $amount, 'Credit Card Payment', $user->balance, Carbon::now());
                    
                    
                    return back()->with('success', $message);
                        
            } else {
                
                return back()->with('alert', 'Account balance is insufficient');
                
            }
            
        } else {
                return back()->with('alert', 'Invalid pin.');
        }        
    }
    
    public function epinSubmit(Request $request)
    {
        
        $quantity = $request->quantity;
        $network = $request->network;
        $amount = $request->amount;
        $amount_to_deduct = $amount * $quantity;
        
        $set= Settings::first();
        $api_key = $set->clubkonnect_api_key;
        $api_id = $set->clubkonnect_user_id;
        
        
        $user=$data['user']=User::find(Auth::user()->id);
        
        if($user->pin == $request->pin){
            if($user->balance>$amount_to_deduct || $user->balance==$amount_to_deduct){
                
                $baseUrl = "https://www.nellobytesystems.com/APIEPINV1.asp?UserID=CK100036514&APIKey=U1GO44135P91C6BOPX707C3V3Z2L39V9Z2A5O6PR2I1JN21KI9ZXHR4L6QR9052N&MobileNetwork=01&Value=100&Quantity=1&CallBackURL=#";
                
                $httpVerb = "GET";
                $contentType = "application/json"; //e.g charset=utf-8
                $headers = array (
                    "Content-Type: $contentType",
                );
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_URL, $baseUrl);
                curl_setopt($ch, CURLOPT_HTTPGET, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $content = curl_exec( $ch );
                $err     = curl_errno( $ch );
                $errmsg  = curl_error( $ch );
            	curl_close($ch);
            	
            	echo $content;
                        
            } else {
                
                return back()->with('alert', 'Account balance is insufficient');
                
            }
            
        } else {
                return back()->with('alert', 'Invalid pin.');
        }        
    }
    
    public function epinSubmitNew(Request $request)
    {
        
        $quantity = $request->quantity;
        $network = $request->type;
        $amount = $request->amount;
        $amount_to_deduct = $amount * $quantity;
        
        $set= Settings::first();
        
        $user=$data['user']=User::find(Auth::user()->id);
        
        if($user->pin == $request->pin){
            if($user->balance>$amount_to_deduct || $user->balance==$amount_to_deduct){
                
                $e_pin = EpinGenerator::where(['amount' => $amount, 'type_code' => $network])->get();
                $pin = EPin::find($network);
                
                if(count($e_pin) > 0){
                    if(count($e_pin) >= $quantity){
                        
                        $e_pin = EpinGenerator::where(['amount' => $amount, 'type_code' => $network])->offset(0)->limit($quantity)->get();
                        
                        $html = '';
                        
                        foreach($e_pin as $ep){
                            
                            $ep->user_id = Auth::user()->id;
                            $ep->status = 1;
                            $ep->save();
                            
                            $html .= '<p>Varspay Technology<br>';
                            $html .= 'Pin No: '.$ep->pin_no.'<br>';
                            $html .= 'Serial No: '.$ep->serial_no.'</p>';
                            
                        }
                        
                        //$new = $amount - (($amount * 3) / 100);
                        $b = $user->balance - $amount_to_deduct;
                        $user->balance = $b;
                        $user->save();
                        event(new SystemLogEvent("User debited", Auth::user()->id));
                    
                        $token = round(microtime(true));
                        $content = 'Acct:'.$user->acct_no.', Date:'.Carbon::now().', DR Amt:'.$amount_to_deduct.',
                            Bal:'.$user->balance.', Ref:'.$token.', Desc: E-Pin';
                        $debit['user_id']=Auth::user()->id;
                        $debit['amount']=$amount_to_deduct;
                        $debit['details']=$content;
                        $debit['type']=1;
                        $debit['seen']=0;
                        $debit['status']=1;
                        $debit['reference']=$token;
                        Alerts::create($debit);
                        
                        send_email($user->email, $user->name, 'E-Pin', $html);
                        
                        return redirect()->route('user.myepin')->with('success', 'E-Pin purchased successfully');
                        
                    } else {
                        return back()->with('alert', 'There is low quantity of '.$pin->name.', you can request for something less');
                    }
                } else {
                    return back()->with('alert', $pin->name.' is not available, check back later');
                }

            } else {
                
                return back()->with('alert', 'Account balance is insufficient');
                
            }
            
        } else {
                return back()->with('alert', 'Invalid pin.');
        }        
    }
    
    public function ajaxAirtimeSwap(Request $request){
        
        $network = Internet::where('code', $request->network)->get();
        $number = AirtimeSwap::where('user_id', Auth::user()->id)->where('status', '0')->get()->count();
        
        if($number<1){
            
            if($network[0]->swap == 1){
                
                $airtimeswap = new AirtimeSwap();
                $airtimeswap->user_id = Auth::user()->id;
                $airtimeswap->status = "0";
                $airtimeswap->amount = $request->amount;
                $airtimeswap->amount_to_receive = $request->amount_to_receive;
                $airtimeswap->phone = $request->number;
                
                if($airtimeswap->save()){
                    
                    echo '<div class="alert alert-primary alert-dismissible fade show" role="alert">
                        <span class="alert-icon"><i class="ni ni-like-2"></i></span>
                        <span class="alert-text">Your are to send #'.$request->amount.' to this number <strong>'.$network[0]->number.'</strong></span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">×</span>
                        </button>
                      </div>';
                }
                
            } else {
                
                echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <span class="alert-icon"><i class="ni ni-like-2"></i></span>
                    <span class="alert-text"><strong>Sorry!</strong> Airtime Swap is not available for this network</span>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">×</span>
                    </button>
                  </div>';
                  
            }
            
        } else {
            
            echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                <span class="alert-icon"><i class="ni ni-like-2"></i></span>
                <span class="alert-text"><strong>Sorry!</strong> Please complete your last transaction on AirtimeSwap</span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>';
        }
        
    }
    
    
    public function ajaxEpinQuantity(Request $request){
        
        $network = EpinGenerator::where(['amount'=> $request->amount, 'type_code' => $request->type, 'status' => 0])->get()->count();
        
        echo '<p>'.$network.' available epins</p>';
    }
    
    
        
    public function plans()
    {
        $data['title']='PY scheme';
        $data['plan']=Plans::whereStatus(1)->orderBy('min_deposit', 'DESC')->get();
        $data['profit']=Profits::whereUser_id(Auth::user()->id)->orderBy('id', 'DESC')->get();
        $data['datetime']=Carbon::now();
        event(new SystemLogEvent("Checked in to PY Scheme", Auth::user()->id));
        return view('user.plans', $data);
    } 
        
    public function fund()
    {
        $data['title']='Fund account';
        $data['adminbank']=Adminbank::whereId(1)->first();
        $data['gateways']=Gateway::whereStatus(1)->orderBy('id', 'DESC')->get();
        $data['deposits']=Deposits::whereUser_id(Auth::user()->id)->latest()->get();
        $data['plan']=Chart::find(12);
        $data['bank_transfer']=Banktransfer::whereUser_id(Auth::user()->id)->orderBy('id', 'DESC')->get();
        event(new SystemLogEvent("Checked in to FUnd Account", Auth::user()->id));
        return view('user.fund', $data);
    }
    
    
    public function transfertransact()
    {
        $data['title']='Transaction History';
        $data['transact']=Int_transfer::where('user_id', Auth::user()->id)->orderBy('created_at', 'DESC')->get();
        $data['bank_transfer']=Banktransfer::whereUser_id(Auth::user()->id)->orderBy('id', 'DESC')->get();
        event(new SystemLogEvent("Checked in to Transfer Transaction History", Auth::user()->id));
        return view('user.transfer_transaction', $data);
    }
        
    public function withdraw()
    {
        $data['title']='Withdraw';
        $data['method']=Withdrawm::whereStatus(1)->get();
        $data['withdraw']=Withdraw::whereUser_id(Auth::user()->id)->orderBy('id', 'DESC')->get();
        event(new SystemLogEvent("Checked in to Withdraw", Auth::user()->id));
        return view('user.withdraw', $data);
    }
    
    public function cardlessWithdraw()
    {
        $data['title']='Cardless Withdraw';
        $data['withdraw']=CardlessWithdraw::whereUser_id(Auth::user()->id)->orderBy('id', 'DESC')->get();
        event(new SystemLogEvent("Checked in to Cardless Withdraw", Auth::user()->id));
        return view('user.cardless_withdraw', $data);
    }
        
    public function bank_transfer()
    {
        $data['title']='Bank transfer';
        $data['bank']=Adminbank::whereId(1)->first();
        event(new SystemLogEvent("Checked in to Bank Transfer", Auth::user()->id));
        return view('user.bank_transfer', $data);
    }
    
    public function card_payment()
    {
        $data['title']='Card Payment';
        $data['set']=Settings::first();
        event(new SystemLogEvent("Checked in to Card Payment", Auth::user()->id));
        return view('user.card_payment', $data);
    }
        
    public function changePassword()
    {
        $data['title'] = "Security";
        $g=new \Sonata\GoogleAuthenticator\GoogleAuthenticator();
        $secret=$g->generateSecret();
        $set=Settings::first();
        $user = User::find(Auth::user()->id);
        $site=$set->site_name;
        $data['secret']=$secret;
        $data['image']=\Sonata\GoogleAuthenticator\GoogleQrUrl::generate($user->email, $secret, $site);
        event(new SystemLogEvent("Checked in to Security", Auth::user()->id));
        return view('user.password', $data);
    } 

    public function changePin()
    {
        $data['title'] = "Change Pin";
        event(new SystemLogEvent("Checked in to Change Pin", Auth::user()->id));
        return view('user.pin', $data);
    } 
    
    public function developer()
    {
        $data['title'] = "Developer";
        $data["api_key"] = ApiKey::where('user_id', Auth::user()->id)->first();
        event(new SystemLogEvent("Checked in to Developer", Auth::user()->id));
        return view('user.developer', $data);
    }
    
    public function submitApiKey(Request $request){
        
        if(!empty(Auth::user()->kyc_bvn) && Auth::user()->kyc_status){
            
            $api_key = new ApiKey;
            $api_key->user_id = Auth::user()->id;
            $api_key->apikey = "vp_live_".md5(Auth::user()->username.Auth::user()->kyc_bvn);
            $api_key->save();
            event(new SystemLogEvent("Api Key generated successfulyy", Auth::user()->id));
            return back()->with('success', 'Api Key Generated Successfully.');
            
        } else {
            
            event(new SystemLogEvent("Api key generated failed", Auth::user()->id));
            return back()->with('alert', 'Please complete your KYC registration');
            
        }
        
    }
        
    public function profile()
    {
        $data['title'] = "Profile";
        event(new SystemLogEvent("Checked in to Profile", Auth::user()->id));
        return view('user.profile', $data);
    } 
    
    public function posForm()
    {
        $data['posformdata'] = PosForm::where('user_id', Auth::id())->get();
        $data['title'] = "Apply for POS Form";
        
        return view('user.posform', $data);
    }
    
    public function ownbank()
    {
        $data['title'] = "Own bank";
        event(new SystemLogEvent("Checked in to Own Bank", Auth::user()->id));
        return view('user.own_bank', $data);
    }
    
    public function Replyticket($id)
    {
        $data['ticket']=$ticket=Ticket::find($id);
        $data['title']='#'.$ticket->ticket_id;
        $data['reply']=Reply::whereTicket_id($ticket->ticket_id)->get();
        event(new SystemLogEvent("Checked in to reply ticket", Auth::user()->id));
        return view('user.reply-ticket', $data);
    }      
    
    public function Editmerchant($id)
    {
        $data['merchant']=$merchant=Merchant::find($id);
        $data['title']=$merchant->name;
        event(new SystemLogEvent("Checked in to edit merchant: ".$data["title"], Auth::user()->id));
        return view('user.edit-merchant', $data);
    }      
    
    public function Logmerchant($id)
    {
        $data['log']=Exttransfer::whereMerchant_key($id)->get();
        $data['title']='Merchant log';
        event(new SystemLogEvent("Checked in to Merchant Logs", Auth::user()->id));
        return view('user.log-merchant', $data);
    }    

    public function otherbank()
    {

        $data['title'] = "Other bank";
        $data['rbanks'] = $this->rubies->bankList()["banklist"];
        event(new SystemLogEvent("Checked in to Other Bank Transfer", Auth::user()->id));
        return view('user.other_bank', $data);
    }
    
    public function bankcode()
    {

        $data['title'] = "Bank List";
        $data['rbanks'] = $this->rubies->bankList()["banklist"];
        event(new SystemLogEvent("Checked in to Bank List", Auth::user()->id));
        return view('user.bankcode', $data);
    }
    
    public function bulkTransfer()
    {
        $data['title'] = "Bulk Transfer to Other Bank";
        $data['batch_list'] = BatchTransfer::where("user_id", Auth()->user()->id)->paginate(10);
        event(new SystemLogEvent("Checked in to Bulk Transfer", Auth::user()->id));
        return view('user.bulk_transfer', $data);
    }
    
    public function bulkTransferTransaction($id)
    {
        $data['title'] = "Bulk Transfer Transaction List";
        $data['bulk'] = BatchTransfer::find($id);
        $data['bulk_list'] = Int_transfer::where("bulk_id", $id)->get();
        $data['batch_list'] = BatchTransferList::where("batch_id", $id)->get();
        $data['rbanks'] = $this->rubies->bankList()["banklist"];
        event(new SystemLogEvent("Checked in to Bilk Transfer Transaction List", Auth::user()->id));
        return view('user.bulk_transfer_transaction', $data);
    }
    
    public function bulkTransferDelete($id)
    {
        $data = BatchTransferList::find($id);
        $bulk = BatchTransfer::find($data->batch_id);
        $bulk->amount -= $data->amount;
        $bulk->save();
        $data->delete();
        
        event(new SystemLogEvent("Delete One of the Batch Transfer Transaction List", Auth::user()->id));
        
        return back()->with('success', 'Delete successfully');
    }
    
    public function bulkTransferList()
    {
        $data['title'] = "Create List";
        $data['rbanks'] = $this->rubies->bankList()["banklist"];
        $data['batch_list'] = BatchTransfer::where("user_id", Auth()->user()->id)->paginate(10);
        event(new SystemLogEvent("Checked in to Bulk transfer list", Auth::user()->id));
        return view('user.bulk_transfer_list', $data);
    }
    
    public function bulkTransferPost(Request $request){
        
        $batch = BatchTransfer::find($request->id);
        $batch_list = BatchTransferList::where('batch_id', $request->id)->get();
        $set=Settings::first();
        $amountx = BatchTransferList::where('batch_id', $request->id)->sum('amount') + ($set->transfer_chargex * count($batch_list));
        $token = round(microtime(true));
        $user=$data['user']=User::find(Auth::user()->id);
        $loan=$data['loan']=Loan::where('user_id', Auth::user()->id)->where('status', 1)->count();
            
        $int_tran = Int_transfer::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        
        if($user->kyc_status == 0 && $int_tran > 100000){
            
            return back()->with('alert', 'Please complete your KYC application to transfer more than 50k limits');
        }
            
        event(new SystemLogEvent("Processing Bulk Transfer, Transaction ID: ".$token, Auth::user()->id));
            
        
        if(count($batch_list) > 0){
            if($loan<1){
                if($user->pin==$request->pin){
                    if($user->balance>$amountx || $user->balance==$amountx){
                        
                        $ar = [];
                        
                        $token = round(microtime(true));
                        for($i = 0; $i < count($batch_list); $i++){
                
                            $token = $token.$i;
                            $ar[$i]["amount"] = $batch_list[$i]->amount;
                            $ar[$i]["craccount"] = $batch_list[$i]->craccount;
                            $ar[$i]["bankcode"] = $batch_list[$i]->bankcode;
                            $ar[$i]["draccountname"] = $batch_list[$i]->draccountname;
                            $ar[$i]["narration"] = $batch_list[$i]->narration;
                            $ar[$i]["craccountname"] = $batch_list[$i]->craccountname;
                            $ar[$i]["bankname"] = $batch_list[$i]->bankname;
                            $ar[$i]["reference"] = $token;
                            
                        }
                        
                        
                        foreach($ar as $k => $v){
                              
                            $sav['details']='Acct Name:'.$v["craccountname"].', Bank name:'.$v["bankname"];
                            $sav['ref_id']=$v["reference"];
                            $sav['amount']=$v["amount"];
                            $sav['bulk_id'] = $request->id;
                            $sav['acct_no']=$v["craccount"];
                            $sav['bank_name']=$v["bankname"];
                            $sav['bank_code']=$v["bankcode"];
                            $sav['narration']=$v["narration"];
                            $sav['acct_name']=$v["craccountname"];
                            $sav['user_id']=Auth::user()->id;
                            $sav['status']=0;
                            $sav['type']=1;
                            Int_transfer::create($sav);
                                
                        }
                        event(new SystemLogEvent("Bulk transfer initiated", Auth::user()->id));
                        
                        if($set->transfer_request && $amountx >= $set->transfer_request_amount){
                                
                            $b = $user->balance - $amountx;
                            $user->balance = $b;
                            $user->save();
                            
                            $batch = BatchTransfer::find($request->id);
                            $batch->amount = $amountx;
                            $batch->save();
                            
                            event(new SystemLogEvent("Debit user account, Amount: ".$amountx, Auth::user()->id));
                                
                            $link = 'https://superuser.varspay.com/varspay_supper_user/confirm-bulk-transfer/'.$request->id;
                            event(new SystemLogEvent("Transfer initiated and completed", Auth::user()->id));
                            send_email_transfer_request($amountx, $link);
                                
                            return back()->with('success', 'Transfer successfully sent');
                            
                        } else {
                            event(new SystemLogEvent("Bulk transfer sent to the server", Auth::user()->id));
                            
                            $validation = $this->rubies->bulkTransferValidation($batch->reference, $ar);
                            
                            if($validation['responsecode'] == "00" && $validation['totalvalidtransactions'] == $validation['totalbatch']){
                            
                                $transfer = $this->rubies->bulkTransfer($batch->reference, $ar);
                            
                                if($transfer["responsecode"] == "00"){
                                    
                                    $batch = BatchTransfer::find($request->id);
                                    $batch->amount = $amountx;
                                    $batch->status = 3;
                                    $batch->save();
                                    
                                    $user = User::find($batch->user_id);
                                    $b = $user->balance - $amountx;
                                    $user->balance = $b;
                                    $user->save();
                                    event(new SystemLogEvent("Debit User Account, Amount: ".$batch->amount, $user->id));
                                    
                                    event(new SystemLogEvent("Bulk transfer queued for processing", Auth::user()->id));
                                    
                                    return redirect()->route('user.bulktransfer')->with('success', 'Bulk transfer queued for processing');
                                
                                } else {
                                    event(new SystemLogEvent("Something went wrong", Auth::user()->id));
                                    return back()->with('alert', "Something went wrong");
                                }
                                
                            } else {
                                event(new SystemLogEvent($validation['totalvalidtransactions'] ." valid out of ".$validation['totalbatch']." transaction", Auth::user()->id));
                                return back()->with('alert', $validation['totalvalidtransactions'] ." valid out of ".$validation['totalbatch']." transaction");
                            }
                        }
                    } else {
                        return back()->with('alert', 'Account balance is insufficient');
                    }
    
                } else {
                    return back()->with('alert', 'Invalid pin.');
                }
            } else {
                return back()->with('alert', 'Request failed, you have an unpaid loan.');       
            }
        } else {
            return back()->with('alert', 'No recipient in this list');      
        }
    }
    
    public function authCheck()
    {
        if (Auth()->user()->status == '0' && Auth()->user()->email_verify == '1' && Auth()->user()->sms_verify == '1') {
            return redirect()->route('user.dashboard');
        } else {
            $data['title'] = "Authorization";
            event(new SystemLogEvent("Checked in to Authorization", Auth::user()->id));
            return view('user.authorization', $data);
        }
    }

    public function localpreview()
    {
        $data['amount'] = Session::get('Amount');
        $data['acct_no'] = Session::get('Acctno');
        $data['acct_name'] = Session::get('Acctname');
        $data['title']='Transfer Preview';
        event(new SystemLogEvent("Checked in to Transfer Preview", Auth::user()->id));
        return view('user.local_preview', $data);
    }

    public function buyasset()
    {
        $data['title']='Buy currency';
        $data['plan']=Chart::whereStatus(1)->get();
        $data['logs']=Buyer::whereUser_id(Auth::user()->id)->orderBy('created_at', 'DESC')->get();
        event(new SystemLogEvent("Checked in to Buy Currency", Auth::user()->id));
        return view('user.buy_asset', $data);
    }    
    
    public function sellasset()
    {
        $data['title']='Sell currency';
        $data['plan']=Chart::whereStatus(1)->get();
        $data['asset']=Asset::whereUser_id(Auth::user()->id)->get();
        $data['logs']=Seller::whereUser_id(Auth::user()->id)->orderBy('created_at', 'DESC')->get();
        event(new SystemLogEvent("Checked in to Sell Currency", Auth::user()->id));
        return view('user.sell_asset', $data);
    }     
    
    public function exchangeasset()
    {
        $data['title']='Exchange currency';
        $data['plan']=Chart::whereStatus(1)->get();
        $data['asset']=Asset::whereUser_id(Auth::user()->id)->get();
        $data['logs']=Exchange::whereUser_id(Auth::user()->id)->orderBy('created_at', 'DESC')->get();
        event(new SystemLogEvent("Checked in to Exchange Currency", Auth::user()->id));
        return view('user.exchange_asset', $data);
    }     
    
    public function checkasset()
    {
        $data['title']='Confirm currency';
        $data['amount']=$amount=Session::get('Amount');
        $data['from']=$from=Session::get('From');
        $data['to']=$to=Session::get('To');
        $data['fprice']=$fprice=Chart::whereId($from)->first();
        $data['tprice']=$tprice=Chart::whereId($to)->first();
        $data['frate']=$frate=(($amount/$fprice->price)*(100-$fprice->exchange_charge)/100);
        $data['trate']=$trate=$tprice->price*$frate;
        $stock=Asset::where('user_id', Auth::user()->id)->where('plan_id', $from)->first();
        $data['logs']=Exchange::whereUser_id(Auth::user()->id)->orderBy('created_at', 'DESC')->get();
        $data['plan']=Chart::whereStatus(1)->get();
        event(new SystemLogEvent("Checked in to Confirm Currency", Auth::user()->id));
        return view('user.confirm_asset', $data);
    } 

    public function transferasset()
    {
        $data['title']='Transfer asset';
        $data['asset']=Asset::whereUser_id(Auth::user()->id)->get();
        $data['transfer']=Assettransfer::where('sender_id',Auth::user()->id)->orWhere('receiver_id',Auth::user()->id)->orderBy('id', 'DESC')->get();
        event(new SystemLogEvent("Checked in to Transfer Asset", Auth::user()->id));
        return view('user.transfer_asset', $data);
    }

    public function sendVcode(Request $request)
    {
        $user = User::find(Auth::user()->id);

        if (Carbon::parse($user->phone_time)->addMinutes(1) > Carbon::now()) {
            $time = Carbon::parse($user->phone_time)->addMinutes(1);
            $delay = $time->diffInSeconds(Carbon::now());
            $delay = gmdate('i:s', $delay);
            session()->flash('alert', 'You can resend Verification Code after ' . $delay . ' minutes');
        } else {
            $code = strtoupper(Str::random(6));
            $user->phone_time = Carbon::now();
            $user->sms_code = $code;
            $user->save();
            send_sms($user->phone, 'Your Verification Code is ' . $code);

            session()->flash('success', 'Verification Code Send successfully');
        }
        return back();
    }

    public function smsVerify(Request $request)
    {
        $user = User::find(Auth::user()->id);
        if ($user->sms_code == $request->sms_code) {
            $user->phone_verify = 1;
            $user->save();
            session()->flash('success', 'Your Profile has been verfied successfully');
            return redirect()->route('user.dashboard');
        } else {
            session()->flash('alert', 'Verification Code Did not matched');
        }
        return back();
    }    
    

    public function sendEmailVcode(Request $request)
    {
        $user = User::find(Auth::user()->id);

        if (Carbon::parse($user->email_time)->addMinutes(1) > Carbon::now()) {
            $time = Carbon::parse($user->email_time)->addMinutes(1);
            $delay = $time->diffInSeconds(Carbon::now());
            $delay = gmdate('i:s', $delay);
            session()->flash('alert', 'You can resend Verification Code after ' . $delay . ' minutes');
        } else {
            $code = strtoupper(Str::random(6));
            $user->email_time = Carbon::now();
            $user->verification_code = $code;
            $user->save();
            send_email($user->email, $user->username, 'Verificatin Code', 'Your Verification Code is ' . $code);
            session()->flash('success', 'Verification Code Send successfully');
        }
        return back();
    }

    public function postEmailVerify(Request $request)
    {

        $user = User::find(Auth::user()->id);
        if ($user->verification_code == $request->email_code) {
            $user->email_verify = 1;
            $user->save();
            session()->flash('success', 'Your Profile has been verfied successfully');
            
            send_email_welcome($user->email, $user->username, 'Welcome to Varspay Tech');
            return redirect()->route('user.dashboard');
        } else {
            session()->flash('alert', 'Verification Code Did not matched');
        }
        return back();
    }
    
    public function bank_transfersubmit(Request $request)
    {
        $user=$data['user']=User::find(Auth::user()->id);
        $currency=Currency::whereStatus(1)->first();
        $set=Settings::first();
        event(new SystemLogEvent("Processing Bank Transfer", Auth::user()->id));
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . $user->username . '.jpg';
            $location = 'asset/screenshot/' . $filename;
            Image::make($image)->resize(800, 800)->save($location);
            $sav['user_id']=Auth::user()->id;
            $sav['amount']=$request->amount;
            $sav['details']=$request->details;
            $sav['image']=$filename;
            $sav['status'] = 0;
            $sav['trx']=$trx=str_random(16);
            Banktransfer::create($sav);
            event(new SystemLogEvent("Bank Transfer Deposit created Transaction ID: ".$trx, Auth::user()->id));
        	if($set['email_notify']==1){
    			send_email($user->email,$user->username,'Deposit request under review','We are currently reviewing your deposit of '.$request->amount.$currency->name.', once confirmed your balance will be credited automatically.<br>Thanks for working with us.');    			
                send_email($set->email,$set->site_name,'New bank deposit request','Hello admin, you have a new bank deposit request for '.$trx);
            }
            return redirect()->route('user.fund')->with('success', 'Deposit request under review');
        }else{
            event(new SystemLogEvent("Bank Transfer Deposit Failed", Auth::user()->id));
            return back()->with('warning', 'An error occured, please try again later');
        }
    }
    
    public function card_transfersubmit(Request $request)
    {
        if($request->status == "SUCCESS" && $request->paymentStatus == "PAID"){
            
            event(new SystemLogEvent("Processing Card Payment Deposit, Transaction ID: ".$request->reference, Auth::user()->id));
            
            $user=User::find(Auth::user()->id);
            $set=Settings::first();
            $amountx = $request->amount - $set->transfer_chargex;
            $depo['charge'] = 35;
            $depo['user_id'] = Auth::user()->id;
            $depo['gateway_id'] = $set->transfer_chargex;
            $depo['amount'] = $amountx;
            $depo['status'] = 1;
            $depo['reference'] = $request->reference;
            $depo['tranReference'] = $request->tranReference;
            Deposits::create($depo);
            
            $b = $user->balance + $amountx;
            $user->balance= $b;
            $user->save();
            event(new SystemLogEvent("Credit User Account, Amount: ".$amountx, Auth::user()->id));
            
            $content='Acct:'.$user->acct_no.', Date:'.Carbon::now().', CR Amt:'.$amountx.',
                Bal:'.$user->balance.', Ref:'.$request->reference.', Desc: Deposit';
            $debit['user_id']=Auth::user()->id;
            $debit['amount']=$amountx;
            $debit['details']=$content;
            $debit['type']=2;
            $debit['seen']=0;
            $debit['status']=1;
            $debit['reference']=$request->reference;
            Alerts::create($debit);
            event(new SystemLogEvent("Alert Sent", Auth::user()->id));
                    
            if($set['sms_notify']==1){
                send_sms($user->phone, $content);
            }
                    
            $message = "Your card payment has been successfully, ".$user->acct_no." has been credited with ".$amountx."";
            
            $user = User::find(Auth::user()->id);
            
            if($set['email_notify']==1){  
                
                send_alert_email($user->email, $user->username, 'Deposit', 'CR', $user->acct_no, $request->reference, $amountx, 'Deposit', $user->balance, Carbon::now());
                
            }
            
            echo "success";
            
            
        } else {
           
            event(new SystemLogEvent("Card Payment Deposit Failed", Auth::user()->id)); 
            echo "error";
            
        }
    }
    
    public function submitmerchant(Request $request)
    {
        $user=$data['user']=User::find(Auth::user()->id);
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . $user->username . '_business.jpg';
            $location = 'asset/profile/' . $filename;
            Image::make($image)->save($location);
            $sav['user_id']=Auth::user()->id;
            $sav['merchant_key']=str_random(16);
            $sav['site_url']=$request->site_link;
            $sav['image']=$filename;
            $sav['name']=$request->merchant_name;
            $sav['description']=$request->merchant_description;
            $sav['status'] = 0;
            Merchant::create($sav);
            return redirect()->route('user.merchant')->with('alert', 'Successfully created, please wait for admin approval');
        }else{
            return back()->with('warning', 'An error occured, please try again later');
        }
    }
    
    public function submitownbank(Request $request)
    {
        $set=Settings::first();
        $currency=Currency::whereStatus(1)->first();
        $amountx=$request->amount+$set->transfer_charge;
        $token = round(microtime(true));
        
        $user=$data['user']=User::find(Auth::user()->id);
        $loan=$data['loan']=Loan::where('user_id', Auth::user()->id)->where('status', 1)->count();
        
        event(new SystemLogEvent("Processing Varspay Account Transfer, Transaction ID: ".$token, Auth::user()->id));
        
        if($loan<1){
            if($user->pin==$request->pin){
                if($user->username!=$request->acct_no){
                    $count=User::where('username',$request->acct_no)->get();
                    if(count($count)>0){
                        $trans=User::where('username', $request->acct_no)->first();
                        if($user->balance>$amountx || $user->balance==$amountx){
                            Session::put('Amount', $request->amount);
                            Session::put('Acctno', $trans->acct_no);
                            Session::put('Acctname', $trans->name);
                            Session::put('Acctemail', $trans->email);
                            return redirect()->route('user.localpreview'); 
                        }else{
                            return back()->with('alert', 'Account balance is insufficient');
                        }
                    }else{
                        return back()->with('alert', 'Invalid account number.');
                    }
                }else{
                    return back()->with('alert', 'You cant transfer money to the same account.');
                }
            }else{
                return back()->with('alert', 'Invalid pin.');
            }
        }else{
            event(new SystemLogEvent("Varspay Account Transfer Request Failed", Auth::user()->id));
            return back()->with('alert', 'Request failed, you have an unpaid loan.');       
        }
    } 
    
    public function submitlocalpreview(Request $request)
    {
        $set=Settings::first();
        $currency=Currency::whereStatus(1)->first();
        $amountx=$request->amount+$set->transfer_charge;
        $token=str_random(10);
        $user=$data['user']=User::find(Auth::user()->id);
        $trans=User::where('acct_no',$request->acct_no)->first();
        $a=$trans->balance+$request->amount;
        $b=$user->balance-$amountx;
        $trans->balance=$a;
        event(new SystemLogEvent("Credit user account, Amount: ".$amountx, $trans->id));
        
        $trans->save(); 
        $user->balance=$b;
        $user->save();
        
        event(new SystemLogEvent("Debit user account, Amount: ".$amountx, Auth::user()->id));
        
        
        $sav['ref_id']=$token;
        $sav['amount']=$request->amount;
        $sav['sender_id']=Auth::user()->id;
        $sav['receiver_id']=$trans->id;
        $sav['status']=1;
        $sav['type']=1;
        Transfer::create($sav);
        event(new SystemLogEvent("Transfer Successfully", Auth::user()->id));
        
        $contentx='Acct:'.$trans->acct_no.', Date:'.Carbon::now().', CR Amt:'.$request->amount.',
        Bal:'.$trans->balance.', Ref:'.$token.', Desc: Bank transfer';
        $credit['user_id']=$trans->id;
        $credit['amount']=$request->amount;
        $credit['details']=$contentx;
        $credit['type']=2;
        $credit['seen']=0;
        $credit['status']=1;
        $credit['reference']=$token;
        Alerts::create($credit);
        event(new SystemLogEvent("Alert Sent", Auth::user()->id));
        
        if($set->sms_notify==1){
            send_sms($trans->phone, $contentx);
        }    
        if($set['email_notify']==1){
            
            send_alert_email($trans->email, $trans->username, 'Credit Alert', 'CR', $trans->acct_no, $token, $request->amount, 'Credit Alert', $trans->balance, Carbon::now());
        }
        $content='Acct:'.$user->acct_no.', Date:'.Carbon::now().', DR Amt:'.$request->amount.',
        Bal:'.$user->balance.', Ref:'.$token.', Desc: Bank transfer';
        $debit['user_id']=Auth::user()->id;
        $debit['amount']=$amountx;
        $debit['details']=$content;
        $debit['type']=1;
        $debit['seen']=0;
        $debit['status']=1;
        $debit['reference']=$token;
        Alerts::create($debit);
        if($set->sms_notify==1){
            send_sms($user->phone, $content);
        }
        if($set['email_notify']==1){
            send_alert_email($user->email, $user->username, 'Debit Alert', 'DR', $user->acct_no, $token, $request->amount, 'Debit Alert', $user->balance, Carbon::now());
        }

        return redirect()->route('user.statement')->with('success', 'Transfer was successful');

    } 

    public function submitotherbank(Request $request)
    {
        $set=Settings::first();
        $amountx=$request->amount+$set->transfer_chargex;
        $token = round(microtime(true));
        $user=$data['user']=User::find(Auth::user()->id);
        $loan=$data['loan']=Loan::where('user_id', Auth::user()->id)->where('status', 1)->count();
        if($loan<1){
            if($user->pin==$request->pin){
                if($user->acct_no!=$request->acct_no){
                    if($user->balance>$amountx || $user->balance==$amountx){
                        
                        $narr = $set->site_name."/".$request->description; 
                        
                        $transfer = $this->providus->singleTransfer($request->amount, $request->bank, $token, $narr, $request->acct_no);
                        
                        if($transfer["requestSuccessful"] && $transfer["responseBody"]["status"] == "SUCCESS"){
                           
                            $b=$user->balance-$amountx;
                            $user->balance=$b;
                            $user->save();
                            $sav['details']='Acct name:'.$request->name .', Bank name:'.$this->providus->bankName($request->bank);
                            $sav['ref_id']=$token;
                            $sav['amount']=$amountx;
                            $sav['acct_no']=$request->acct_no;
                            $sav['bank_name']=$this->providus->bankName($request->bank);
                            $sav['user_id']=Auth::user()->id;
                            $sav['status']=1;
                            $sav['type']=1;
                            Int_transfer::create($sav);
                        

                            $content='Acct:'.$user->acct_no.', Date:'.Carbon::now().', DR Amt:'.$amountx.',
                                Bal:'.$user->balance.', Ref:'.$token.', Desc: Bank transfer';
                            $debit['user_id']=Auth::user()->id;
                            $debit['amount']=$amountx;
                            $debit['details']=$content;
                            $debit['type']=1;
                            $debit['seen']=0;
                            $debit['status']=1;
                            $debit['reference']=$token;
                            Alerts::create($debit);
                            if($set->sms_notify==1){
                            send_sms($user->phone, $content);
                            }
                            if($set['email_notify']==1){
                                send_alert_email($user->email, $user->username, 'Debit Alert', 'DR', $user->acct_no, $token, $amountx, 'Debit Alert', $user->balance, Carbon::now());
                            }
                            
                            return redirect()->route('user.statement')->with('success', 'Transfer successfully sent');
                        
                        } else {
                            
                            return back()->with('alert', $transfer["responseMessage"]);
   
                        }

                    }else{
                        return back()->with('alert', 'Account balance is insufficient');
                    }
                }else{
                    return back()->with('alert', 'You cant transfer money to thesame account.');
                }
            }else{
                return back()->with('alert', 'Invalid pin.');
            }
        }else{
            return back()->with('alert', 'Request failed, you have an unpaid loan.');       
        }
    }
    
    public function submitotherbankrubies(Request $request)
    {
        $set=Settings::first();
        $amountx=$request->amount+$set->transfer_chargex;
        $token = round(microtime(true));
        $user=$data['user']=User::find(Auth::user()->id);
        $loan=$data['loan']=Loan::where('user_id', Auth::user()->id)->where('status', 1)->count();
        $int_tran = Int_transfer::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
        
        if($user->kyc_status == 0 && $int_tran > 100000){
            
            return back()->with('alert', 'Please complete your KYC application to transfer more than 50k limits');
            
        }
        
        event(new SystemLogEvent("Processing Other Bank Transfer, Transaction ID: ".$token, Auth::user()->id));
        
        if($loan<1){
            if($user->pin==$request->pin){
                if($user->acct_no!=$request->acct_no){
                    if($user->balance>$amountx || $user->balance==$amountx){
                        
                        //$narr = $set->site_name." ".$request->description; 
                        
                        if($set->transfer_request && $amountx >= $set->transfer_request_amount){
                            
                            event(new SystemLogEvent("Server response successfully", Auth::user()->id));
                                
                                $b=$user->balance-$amountx;
                                $user->balance=$b;
                                $user->save();
                                event(new SystemLogEvent("Debit user account, Amount: ".$amountx, Auth::user()->id));
                                
                                
                                $int_transfer = new Int_transfer;
                                $int_transfer->details='Acct name:'.$request->name .', Bank name:'.$this->rubies->bankName($request->bank);
                                $int_transfer->ref_id=$token;
                                $int_transfer->amount=$amountx;
                                $int_transfer->acct_no=$request->acct_no;
                                $int_transfer->bank_name=$this->rubies->bankName($request->bank);
                                $int_transfer->user_id=Auth::user()->id;
                                $int_transfer->bank_code=$request->bank;
                                $int_transfer->narration=$request->description;
                                $int_transfer->acct_name=$request->name;
                                $int_transfer->status=0;
                                $int_transfer->type=1;
                                $int_transfer->save();
                                $link = 'https://superuser.varspay.com/varspay_supper_user/confirm-transfer/'.$int_transfer->id;
                                event(new SystemLogEvent("Transfer initiated and completed", Auth::user()->id));
                                send_email_transfer_request($amountx, $link);
                                
                                return redirect()->route('user.statement')->with('success', 'Transfer successfully sent');
                            
                            
                        } else {
                            
                            event(new SystemLogEvent("Request sent to transfer server", Auth::user()->id));
                            $transfer = $this->rubies->singleTransfer($request->amount, $request->description, $request->name, $this->rubies->bankName($request->bank), $set->site_name, $request->acct_no, $request->bank, $token);
                            
                            if($transfer["responsecode"] == "00" && $transfer["responsemessage"] == "Success"){
                                
                                event(new SystemLogEvent("Server response successfully", Auth::user()->id));
                                
                                $b=$user->balance-$amountx;
                                $user->balance=$b;
                                $user->save();
                                event(new SystemLogEvent("Debit user account, Amount: ".$amountx, Auth::user()->id));
                                
                                $sav['details']='Acct name:'.$request->name .', Bank name:'.$this->rubies->bankName($request->bank);
                                $sav['ref_id']=$token;
                                $sav['amount']=$amountx;
                                $sav['acct_no']=$request->acct_no;
                                $sav['bank_name']=$this->rubies->bankName($request->bank);
                                $sav['bank_code']=$request->bank;
                                $sav['narration']=$request->description;
                                $sav['acct_name']=$request->name;
                                $sav['user_id']=Auth::user()->id;
                                $sav['sessionid'] = $transfer['sessionid'];
                                $sav['status']=1;
                                $sav['type']=1;
                                Int_transfer::create($sav);
                                event(new SystemLogEvent("Transfer initiated and completed", Auth::user()->id));
    
                                $content='Acct:'.$user->acct_no.', Date:'.Carbon::now().', DR Amt:'.$amountx.',
                                    Bal:'.$user->balance.', Ref:'.$token.', Desc: Bank transfer';
                                $debit['user_id']=Auth::user()->id;
                                $debit['amount']=$amountx;
                                $debit['details']=$content;
                                $debit['type']=1;
                                $debit['seen']=0;
                                $debit['status']=1;
                                $debit['reference']=$token;
                                Alerts::create($debit);
                                event(new SystemLogEvent("Alert Sent", Auth::user()->id));
                                if($set->sms_notify==1){
                                send_sms($user->phone, $content);
                                }
                                if($set['email_notify']==1){
                                    send_alert_email($user->email, $user->username, 'Debit Alert', 'DR', $user->acct_no, $token, $amountx, 'Debit Alert', $user->balance, Carbon::now());
                                }
                                
                                return redirect()->route('user.statement')->with('success', 'Transfer successfully sent');
                            
                            } elseif($transfer["responsecode"] == "32"){
                                
                                return back()->with('alert','Unable to debit account');
                                
                            } elseif($transfer["responsecode"] == "33"){
                                
                                event(new SystemLogEvent("Transaction Failed", Auth::user()->id));
                                return back()->with('alert','Transaction Failed');
                                
                                
                            } elseif($transfer["responsecode"] == "96" || $transfer["responsecode"] == "999"){
                                
                                event(new SystemLogEvent("System Malfunction", Auth::user()->id));
                                return back()->with('alert','System Malfunction');
                                
                                
                            } else {
                                
                                event(new SystemLogEvent($transfer["responsemessage"], Auth::user()->id));
                                return back()->with('alert', $transfer["responsemessage"]);
       
                            }
                        }

                    }else{
                        return back()->with('alert', 'Account balance is insufficient');
                    }
                }else{
                    return back()->with('alert', 'You cant transfer money to thesame account.');
                }
            }else{
                return back()->with('alert', 'Invalid pin.');
            }
        }else{
            return back()->with('alert', 'Request failed, you have an unpaid loan.');       
        }
    }
    
    public function submitbulktransferCsv(Request $request)
    {
        if($request->hasFile('bulkFile')){
            
            $file = $request->file('bulkFile');
            $xlsx = new SimpleXLSX();
            $ar = $xlsx::parse($file);
            
            $cell = $ar->rows();
            
            unset($cell[0]);
            
            $the_array = array();
            foreach($cell as $value) {
                
                $the_array[] = $value; 
                
            } 
            
            $token = round(microtime(true));
            $cur = "NGN";
            for($i = 0; $i < count($the_array); $i++){
                
                $token = $token.$i;
                array_push($the_array[$i], $token);
                array_push($the_array[$i], $cur);
                
            }
            
            $keys = array("destinationBankCode", "destinationAccountNumber", "narration", "amount", "reference", "currency");

            $new_ar = array();
            
            for($i = 0; $i < count($the_array); $i++){
                
                $new = array_combine($keys, $the_array[$i]);
                
                $new_ar[$i] = $new;
                
            }
            $set=Settings::first();
            foreach ($new_ar as $k => $v) {
              if ($v['destinationBankCode'] != '') {
                $new_ar[$k]['destinationBankCode'] = $this->providus->bankCode($v['destinationBankCode']);
              }
              $new_ar[$k]['narration'] = $set->site_name."/".$v['narration'];
            }
            
            $totalAmount = 0;
        
            for($i = 0; $i < count($new_ar); $i++){
                $totalAmount += intval($new_ar[$i]["amount"]);
            }
            
            $set=Settings::first();
            $amountx=$totalAmount+($set->transfer_chargex * count($new_ar));
            $token = round(microtime(true));
            $user=$data['user']=User::find(Auth::user()->id);
            $loan=$data['loan']=Loan::where('user_id', Auth::user()->id)->where('status', 1)->count();
            if($loan<1){
                if($user->pin==$request->pin){
                    if($user->acct_no!= $request->acct_no){
                        if($user->balance>$amountx || $user->balance==$amountx){
                            
                            $batch = new BatchTransfer;
                            $batch->title = "Bulk Transfer";
                            $batch->reference = "batch-".round(microtime(true));
                            $batch->status = 0;
                            $batch->user_id=Auth::user()->id;
                            $batch->amount = $amountx;
                            $batch->save();
                            
                            foreach($new_ar as $k => $v){
                                
                                $sav['details']='Acct name: , Bank name:'.$this->providus->bankName($v["destinationBankCode"]);
                                //Account name for bulk transfer
                                
                                $sav['ref_id']=$v["reference"];
                                $sav['amount']=$v["amount"];
                                $sav['bulk_id'] = $batch->id;
                                $sav['acct_no']=$v["destinationAccountNumber"];
                                $sav['bank_name']=$this->providus->bankName($v["destinationBankCode"]);
                                $sav['user_id']=Auth::user()->id;
                                $sav['status']=0;
                                $sav['type']=1;
                                Int_transfer::create($sav);
                                
                            }
                            
                            $narr = $set->site_name."/".$request->description;
                            
                            $transfer = $this->providus->bulkTransfer($batch->reference, $narr, $new_ar);
                            
                            if($transfer["requestSuccessful"] && $transfer["responseBody"]["batchStatus"] == "AWAITING_PROCESSING"){
                                
                                return redirect()->route('user.bulktransfer')->with('success', 'Transfer successfully sent, await confirmation');
                                
                            } else {
                                return back()->with('alert', $transfer["responseMessage"]);
                            }
                            
                        } else {
                            return back()->with('alert', 'Account balance is insufficient');
                        }
                    } else {
                        return back()->with('alert', 'You cant transfer money to thesame account.');
                    }
                } else {
                    return back()->with('alert', 'Invalid pin.');
                }
            } else {
                return back()->with('alert', 'Request failed, you have an unpaid loan.');       
            }
            
        } else {
         
            return back()->with('alert', 'Please select csv file to proceed for bulk transfer');
         
        }
    }
    
    public function submitbulktransferRubiesCsv(Request $request)
    {
        if($request->hasFile('bulkFile')){
            
            $file = $request->file('bulkFile');
            $xlsx = new SimpleXLSX();
            $ar = $xlsx::parse($file);
            
            $cell = $ar->rows();
            
            unset($cell[0]);
            
            $the_array = array();
            foreach($cell as $value) {
                
                $the_array[] = $value; 
                
            } 
            
            $set=Settings::first();
            
            $token = round(microtime(true));
            
            for($i = 0; $i < count($the_array); $i++){
                
                $token = $token.$i;
                array_push($the_array[$i], $token);
                array_push($the_array[$i], $set->site_name);
                array_push($the_array[$i], "rubies");
                array_push($the_array[$i], "varspay");
                
            }
            
            $keys = array("bankcode", "craccount", "narration", "amount", "reference", "draccountname", "bankname", "craccountname");

            $new_ar = array();
            
            for($i = 0; $i < count($the_array); $i++){
                
                if(!array_combine($keys, $the_array[$i])){
                    
                    break;
                    break;
                    
                    return back()->with('alert', 'Invalid format, Kindly download our format and upload accordingly');
                }
                
                $new = array_combine($keys, $the_array[$i]);
                
                $new_ar[$i] = $new;
                
            }
            
            
            $set=Settings::first();
            foreach ($new_ar as $k => $v) {
              if ($v['bankname'] != '') {
                    
                $new_ar[$k]['bankname'] = $this->rubies->bankName($v['bankcode']);

              }
              
              $new_ar[$k]['craccountname'] = $this->rubies->acctName($v['craccount'],$v['bankcode']);
              $new_ar[$k]['narration'] = $request->description;
            }
            
            $totalAmount = 0;
        
            for($i = 0; $i < count($new_ar); $i++){
                $totalAmount += intval($new_ar[$i]["amount"]);
            }
            
            $set=Settings::first();
            $amountx=$totalAmount+($set->transfer_chargex * count($new_ar));
            
            event(new SystemLogEvent("Bulk transfer processing", Auth::user()->id));

            $batch = new BatchTransfer;
            $batch->title = $request->name;
            $batch->reference = "batch".round(microtime(true));
            $batch->status = 0;
            $batch->user_id=Auth::user()->id;
            $batch->amount = $amountx;
            $batch->save();
                                
            foreach($new_ar as $k => $v){
                
                $sav['draccountname']=$v["draccountname"];
                $sav['craccountname']=$v["craccountname"];
                $sav['reference']=$v["reference"];
                $sav['amount']=$v["amount"];
                $sav['batch_id'] = $batch->id;
                $sav['craccount']=$v["craccount"];
                $sav['bankcode']=$v["bankcode"];
                $sav['narration'] = $v['narration'];
                $sav['bankname']=$v["bankname"];
                $sav['user_id']=Auth::user()->id;
                BatchTransferList::create($sav);
                                    
            }
            
            event(new SystemLogEvent("Bulk transfer upload successfully", Auth::user()->id));
            
            return redirect()->route('user.bulktransfertransaction', $batch->id)->with('success', 'Batch uploaded successfully');
            
            
        } else {
         
            return back()->with('alert', 'Please select csv file to proceed for bulk transfer');
         
        }
    }
    
    public function bulkTransferAdd(Request $request)
    {
        $set=Settings::first();
        $list = BatchTransferList::where('batch_id', $request->batch_id)->get();
        $no = count($list) + 1;
        $token = round(microtime(true));
        $sav['draccountname']=$set->site_name;
        $sav['craccountname']=$request->name;
        $sav['reference']=$token.$no;
        $sav['amount']=$request->amount;
        $sav['batch_id'] = $request->batch_id;
        $sav['craccount']=$request->acct_no;
        $sav['bankcode']=$request->bank;
        $sav['narration'] = $request->description;
        $sav['bankname']=$this->rubies->bankName($request->bank);
        $sav['user_id']=Auth::user()->id;
        
        if(BatchTransferList::create($sav)){
            event(new SystemLogEvent("Add recipient to Batch Transfer List", Auth::user()->id));
            
            return back()->with('success', 'Added successfully');
        } else {
            return back()->with('alert', 'something went wrong');
        }
    }
    
    public function submitbulktransferList(Request $request)
    {
     
        $ar = [];
        $token = round(microtime(true));
        $set=Settings::first();
        for($i = 0; $i < count($request->acct_no); $i++){
            
            $token = $token.$i;
            $ar[$i]["amount"] = $request->amount[$i];
            $ar[$i]["destinationAccountNumber"] = $request->acct_no[$i];
            $ar[$i]["destinationBankCode"] = $request->bank[$i];
            $ar[$i]["narration"] = $set->site_name."/".$request->description;
            $ar[$i]["currency"] = "NGN";
            $ar[$i]["reference"] = $token;
            
        }
        
        $totalAmount = 0;
        
        for($i = 0; $i < count($ar); $i++){
            $totalAmount += intval($ar[$i]["amount"]);
        }

        $set=Settings::first();
        $amountx=$totalAmount+($set->transfer_chargex * count($new_ar));
        $token = round(microtime(true));
        $user=$data['user']=User::find(Auth::user()->id);
        $loan=$data['loan']=Loan::where('user_id', Auth::user()->id)->where('status', 1)->count();
        if($loan<1){
            if($user->pin==$request->pin){
                if($user->acct_no!= $request->acct_no){
                    if($user->balance>$amountx || $user->balance==$amountx){
                        
                        $batch = new BatchTransfer;
                        $batch->title = "Bulk Transfer";
                        $batch->reference = "batch-".round(microtime(true));
                        $batch->status = 0;
                        $batch->user_id=Auth::user()->id;
                        $batch->amount = $amountx;
                        $batch->save();
                            
                        foreach($ar as $k => $v){
                                
                            $sav['details']='Bulk Transfer, Bank name:'.$this->providus->bankName($v["destinationBankCode"]);
                            $sav['ref_id']=$v["reference"];
                            $sav['amount']=$v["amount"];
                            $sav['bulk_id'] = $batch->id;
                            $sav['acct_no']=$v["destinationAccountNumber"];
                            $sav['bank_name']=$this->providus->bankName($v["destinationBankCode"]);
                            $sav['user_id']=Auth::user()->id;
                            $sav['status']=0;
                            $sav['type']=1;
                            Int_transfer::create($sav);
                                
                        }
                        
                        $narr = $set->site_name."/".$request->description;
                        
                        $transfer = $this->providus->bulkTransfer($batch->reference, $narr, $ar);
                        
                        if($transfer["requestSuccessful"] && $transfer["responseBody"]["batchStatus"] == "AWAITING_PROCESSING"){
                           
                            
                            return redirect()->route('user.bulktransfer')->with('success', 'Transfer successfully sent, await confirmation');
                        
                        } else {
                            
                            return back()->with('alert', $transfer["responseMessage"]);
   
                        }
                            
                    } else {
                        return back()->with('alert', 'Account balance is insufficient');
                    }
                } else {
                    return back()->with('alert', 'You cant transfer money to thesame account.');
                }
            } else {
                return back()->with('alert', 'Invalid pin.');
            }
        } else {
            return back()->with('alert', 'Request failed, you have an unpaid loan.');       
        }
        
    }
    
    
    public function submitbulktransferRubiesList(Request $request)
    {
     
        $ar = [];
        $token = round(microtime(true));
        $set=Settings::first();
        for($i = 0; $i < count($request->acct_no); $i++){
            
            $token = $token.$i;
            $ar[$i]["amount"] = $request->amount[$i];
            $ar[$i]["craccount"] = $request->acct_no[$i];
            $ar[$i]["bankcode"] = $request->bank[$i];
            $ar[$i]["draccountname"] = $set->site_name;
            $ar[$i]["narration"] = $request->description;
            $ar[$i]["craccountname"] = $this->rubies->acctName($request->acct_no[$i],$request->bank[$i]);
            $ar[$i]["bankname"] = $this->rubies->bankName($request->bank[$i]);
            $ar[$i]["reference"] = $token;
            
        }
        
        $totalAmount = 0;
        
        for($i = 0; $i < count($ar); $i++){
            $totalAmount += intval($ar[$i]["amount"]);
        }
        
        $set=Settings::first();
        $amountx=$totalAmount+($set->transfer_chargex * count($ar));

        event(new SystemLogEvent("Bulk transfer processing", Auth::user()->id));

        $batch = new BatchTransfer;
        $batch->title = $request->name;
        $batch->reference = "batch".round(microtime(true));
        $batch->status = 0;
        $batch->user_id=Auth::user()->id;
        $batch->amount = $amountx;
        $batch->save();
                            
        foreach($ar as $k => $v){
            
            $sav['draccountname']=$v["draccountname"];
            $sav['craccountname']=$v["craccountname"];
            $sav['reference']=$v["reference"];
            $sav['amount']=$v["amount"];
            $sav['batch_id'] = $batch->id;
            $sav['craccount']=$v["craccount"];
            $sav['bankcode']=$v["bankcode"];
            $sav['narration'] = $v['narration'];
            $sav['bankname']=$v['bankname'];
            $sav['user_id']=Auth::user()->id;
            BatchTransferList::create($sav);
                                
        }
        
        event(new SystemLogEvent("Bulk transfer upload successfully", Auth::user()->id));
        
        return redirect()->route('user.bulktransfertransaction', $batch->id)->with('success', 'Batch uploaded successfully');
    }
    
    public function webhookBulkConfirm(Request $request){
        
        $batchs = BatchTransfer::get();
        
        foreach($batchs as $batch){
            
            if($batch->status == 1){
                continue;
            }
            
            $response = $this->providus->bulkTransferDetails($batch->reference);
            
            if($response["requestSuccessful"] && $response["responseBody"]["batchStatus"] == "COMPLETED"){
            
                $batch->status = 1;
                $batch->save();
                
                $bulk_list = Int_transfer::where("bulk_id", $batch->id)->get();
                
                foreach($bulk_list as $bl){
                    
                    $bl->status = 1;
                    $bl->save();
                }
                
                $set=Settings::first();
                $user=$data['user']=User::find($batch->user_id);
                $b=$user->balance-$batch->amount;
                $user->balance=$b;
                $user->save();
                            
                $content='Acct:'.$user->acct_no.', Date:'.Carbon::now().', DR Amt:'.$batch->amount.',
                Bal:'.$user->balance.', Ref:'.$batch->reference.', Desc: Bulk transfer';
                $debit['user_id']=Auth::user()->id;
                $debit['amount']=$batch->amount;
                $debit['details']=$content;
                $debit['type']=1;
                $debit['seen']=0;
                $debit['status']=1;
                $debit['reference']=$batch->reference;
                Alerts::create($debit);
                if($set->sms_notify==1){
                    send_sms($user->phone, $content);
                }
                if($set['email_notify']==1){
                    send_alert_email($user->email, $user->username, 'Debit Alert', 'DR', $user->acct_no, $batch->reference, $batch->amount, 'Bulk Transfer Debit Alert', $user->balance, Carbon::now());
                }
            }
            
        }
        
        echo "Thanks";
        
    }
    
    
    public function webhookBulkRubiesConfirm(Request $request){
        
        $batchs = BatchTransfer::where('status', 3)->get();
        
        foreach($batchs as $batch){
            
            if($batch->status == 1){
                continue;
            }
            
            $response = $this->rubies->bulkTransferDetails($batch->reference);
            
            if($response["responsecode"] == "00" && $response["batchstatus"] == "COMPLETED"){
            
                $batch->status = 1;
                $batch->save();
                
                foreach($response["transactions"] as $tran){
                    
                    $bulk_list = Int_transfer::where("bulk_id", $batch->id)->where('ref_id', $tran["reference"])->first();
                    
                    if($tran["reference"] == $bulk_list->ref_id && $tran["responsecode"] == "00"){
                        
                        if($bulk_list->status == 1){
                            
                            continue;
                            
                        } else {
                            $bulk_list->status = 1;
                            $bulk_list->sessionid = $tran["sessionid"];
                            $bulk_list->save();
                        }
                        
                    } elseif($tran["reference"] == $bulk_list->ref_id && $tran["responsecode"] !== "00") {
                        
                        $bulk_list->status = 2;
                        $bulk_list->save();
                        
                    }
                }
                
                $user = User::find($batch->user_id);
                $content='Acct:'.$user->acct_no.', Date:'.Carbon::now().', DR Amt:'.$batch->amount.',
                Bal:'.$user->balance.', Ref:'.$batch->reference.', Desc: Bulk transfer';
                $debit['user_id']=$batch->user_id;
                $debit['amount']=$batch->amount;
                $debit['details']=$content;
                $debit['type']=1;
                $debit['seen']=0;
                $debit['status']=1;
                $debit['reference']=$batch->reference;
                Alerts::create($debit);
                if($set->sms_notify==1){
                    send_sms($user->phone, $content);
                }
                if($set['email_notify']==1){
                    send_alert_email($user->email, $user->username, 'Debit Alert', 'DR', $user->acct_no, $batch->reference, $batch->amount, 'Bulk Transfer Debit Alert', $user->balance, Carbon::now());
                }
            }
            
        }
        
        echo "Thanks";
        
    }
    
    
    public function webhookBulkTransfer(Request $request){
    
        $response = BatchTransfer::where("reference", $request->input('batchReference'))->first();
        
        if($request->input('batchStatus') == "COMPLETED"){
            
            $response->status = 1;
            $response->save();
            
            $bulk_list = Int_transfer::where("bulk_id", $response->id)->get();
            
            foreach($bulk_list as $bl){
                
                $bl->status = 1;
                $bl->save();
            }
            
            $set=Settings::first();
            $user=$data['user']=User::find($response->user_id);
            $b=$user->balance-$response->amount;
            $user->balance=$b;
            $user->save();
                        
            $content='Acct:'.$user->acct_no.', Date:'.Carbon::now().', DR Amt:'.$response->amount.',
            Bal:'.$user->balance.', Ref:'.$response->reference.', Desc: Bulk transfer';
            $debit['user_id']=Auth::user()->id;
            $debit['amount']=$response->amount;
            $debit['details']=$content;
            $debit['type']=1;
            $debit['seen']=0;
            $debit['status']=1;
            $debit['reference']=$response->reference;
            Alerts::create($debit);
            if($set->sms_notify==1){
                send_sms($user->phone, $content);
            }
            if($set['email_notify']==1){
                send_alert_email($user->email, $user->username, 'Debit Alert', 'DR', $user->acct_no, $response->reference, $response->amount, 'Bulk Transfer Debit Alert', $user->balance, Carbon::now());
            }
        }
    }
    
    public function webhookWalletTransfer(Request $request){
        
        
        $users_reserve_tran = User::get();
        
        $set=Settings::first();
        
        foreach($users_reserve_tran as $reserve_acct){
            
            if(empty($reserve_acct->acct_ref)){
                continue;
            }
            
            $reserve_tran = $this->providus->getResAcctTransaction($reserve_acct->acct_ref);
            
            if($reserve_tran["requestSuccessful"] && $reserve_tran["responseMessage"] == "success"){
                
                foreach($reserve_tran["responseBody"]["content"] as $content){
                    
                    if($content["paymentMethod"] == "ACCOUNT_TRANSFER" && $content["paymentStatus"] == "PAID"){
                        
                        
                        if(Deposits::where('tranReference', $content["transactionReference"])->exists() == false){
                            
                            event(new SystemLogEvent("Wallet Transfer initiated, Transaction Id: ".$content["transactionReference"], Auth::user()->id));
                            
                            $depo['charge'] = $set->transfer_chargex;
                            $depo['user_id'] = $reserve_acct->id;
                            $depo['gateway_id'] = 20;
                            $depo['amount'] = $content["amountPaid"];
                            $depo['status'] = 1;
                            $depo['reference'] = $content["paymentReference"];
                            $depo['tranReference'] = $content["transactionReference"];
                            Deposits::create($depo);
                            
                            
                            $b = $reserve_acct->balance + $content["amountPaid"];
                            $reserve_acct->balance= $b;
                            $reserve_acct->save();
                            event(new SystemLogEvent("Credit User Account, Amount: ".$content["amountPaid"], Auth::user()->id));

                            
                            $contentx='Acct:'.$reserve_acct->acct_no.', Date:'.Carbon::now().', CR Amt:'.$content["amountPaid"].',
                                Bal:'.$reserve_acct->balance.', Ref:'.$content["paymentReference"].', Desc: Deposit';
                            $debit['user_id']=Auth::user()->id;
                            $debit['amount']=$content["amountPaid"];
                            $debit['details']=$contentx;
                            $debit['type']=2;
                            $debit['seen']=0;
                            $debit['status']=1;
                            $debit['reference']=$content["paymentReference"];
                            Alerts::create($debit);
                            event(new SystemLogEvent("Alert Sent", Auth::user()->id));
                                    
                            if($set['sms_notify']==1){
                                send_sms($reserve_acct->phone, $contentx);
                            }
                                    
                            $message = "Your wallet ".$reserve_acct->acct_no.", has been credited with ".$content["amountPaid"]."";
                            
                            $user = User::find($reserve_acct->id);
                            
                            if($set['email_notify']==1){  
                                
                                send_alert_email($user->email, $user->username, 'Deposit', 'CR', $user->acct_no, $content["paymentReference"], $content["amountPaid"], 'Deposit', $user->balance, Carbon::now());
                                
                            }
                            
                        } else {
                            
                            continue;
                            
                        }
                        
                    }
                    
                }
            }
            
        }
        
        echo "Thanks";
        
    }
    
    
    public function webhookRubiesWalletTransfer(Request $request){
        
        
        $users_reserve_tran = User::get();
        
        $set=Settings::first();
        
        foreach($users_reserve_tran as $reserve_acct){
            
            if(empty($reserve_acct->acct_no)){
                continue;
            }
            
            $reserve_tran = $this->rubies->listVirtualAccountTransaction($reserve_acct->acct_no);
            
            if($reserve_tran["responsecode"] == "00" && $reserve_tran["responsemessage"] == "successful"){
                
                foreach($reserve_tran["transactions"] as $content){
                        
                    if(Deposits::where('tranReference', $content["paymentreference"])->exists() == false){
                        
                        event(new SystemLogEvent("Wallet Transfer initiated, Transaction Id: ".$content["paymentreference"], Auth::user()->id));
                            
                        $depo['charge'] = $set->transfer_chargex;
                        $depo['user_id'] = $reserve_acct->id;
                        $depo['gateway_id'] = 21;
                        $depo['amount'] = $content["amount"];
                        $depo['status'] = 1;
                        $depo['reference'] = $content["paymentreference"];
                        $depo['tranReference'] = $content["paymentreference"];
                        Deposits::create($depo);
                            
                        $b = $reserve_acct->balance + $content["amount"];
                        $reserve_acct->balance= $b;
                        $reserve_acct->save();
                        event(new SystemLogEvent("Credit User Account, Amount: ".$content["amount"], Auth::user()->id));

                            
                        $contentx='Acct:'.$reserve_acct->acct_no.', Date:'.Carbon::now().', CR Amt:'.$content["amount"].',
                                Bal:'.$reserve_acct->balance.', Ref:'.$content["paymentreference"].', Desc: Bank Transfer Deposit';
                        $debit['user_id']=Auth::user()->id;
                        $debit['amount']=$content["amount"];
                        $debit['details']=$contentx;
                        $debit['type']=2;
                        $debit['seen']=0;
                        $debit['status']=1;
                        $debit['reference']=$content["paymentreference"];
                        Alerts::create($debit);
                        event(new SystemLogEvent("Alert Sent", Auth::user()->id));   
                        if($set['sms_notify']==1){
                            send_sms($reserve_acct->phone, $contentx);
                        }
                                    
                        $message = "Your wallet ".$reserve_acct->acct_no.", has been credited with ".$content["amount"]."";
                            
                        $user = User::find($reserve_acct->id);
                            
                        if($set['email_notify']==1){  
                                
                            send_alert_email($user->email, $user->username, 'Deposit', 'CR', $user->acct_no, $content["paymentreference"], $content["amount"], 'Deposit', $user->balance, Carbon::now());
                                
                        }
                            
                    } else {
                        
                        continue;
                            
                    }
                    
                }
            }
            
        }
        
        echo "Thanks";
        
    }
    
    public function webhookWalletPayment(Request $request){
    
        if($request->input('paymentMethod') == "ACCOUNT_TRANSFER" && $request->input('product.type') == "RESERVED_ACCOUNT" && $request->input('paymentStatus') == "PAID"){
            
            $user=User::where('acct_ref',$request->input('product.reference'))->first();
            $set=Settings::first();
            $amountx = $request->input('amountPaid') - $set->transfer_chargex;
            $depo['charge'] = $set->transfer_chargex;
            $depo['user_id'] = Auth::user()->id;
            $depo['gateway_id'] = 20;
            $depo['amount'] = $amountx;
            $depo['status'] = 1;
            $depo['reference'] = $request->input('paymentReference');
            $depo['tranReference'] = $request->input('transactionReference');
            Deposits::create($depo);
            
            $b = $user->balance + $amountx;
            $user->balance= $b;
            $user->save();
            
            $content='Acct:'.$user->acct_no.', Date:'.Carbon::now().', CR Amt:'.$amountx.',
                Bal:'.$user->balance.', Ref:'.$request->input('paymentReference').', Desc: Deposit';
            $debit['user_id']=Auth::user()->id;
            $debit['amount']=$amountx;
            $debit['details']=$content;
            $debit['type']=2;
            $debit['seen']=0;
            $debit['status']=1;
            $debit['reference']=$request->input('paymentReference');
            Alerts::create($debit);
                    
            if($set['sms_notify']==1){
                send_sms($user->phone, $content);
            }
                    
            $message = "Your card payment has been successfully, ".$user->acct_no." has been credited with ".$amountx."";
            
            $user = User::find(Auth::user()->id);
            
            if($set['email_notify']==1){  
                
                send_alert_email($user->email, $user->username, 'Deposit', 'CR', $user->acct_no, $request->input('paymentReference'), $amountx, 'Deposit', $user->balance, Carbon::now());
                
            }
            
            echo "success";
            
            
        } else {
            
            echo "error";
            
        }
    }
    
    public function bankupdate(Request $request)
    {
        $user=$data['user']=User::find(Auth::user()->id);
        $count=UserBank::whereUser_id(Auth::user()->id)->count();
        if($count>0){
            $bank=UserBank::whereUser_id(Auth::user()->id)->first();
            $bank->name=$request->name;
            $bank->address=$request->address;
            $bank->bvn=$request->bvn;
            $bank->acct_no=$request->acct_no;
            $bank->acct_name=$request->acct_name;
            $bank->save();
            event(new SystemLogEvent("Bank Details Updated", Auth::user()->id));
            return back()->with('success', 'Bank details was successfully updated');
            
        }else{
            $sav['user_id']=Auth::user()->id;
            $sav['name']=$request->name;
            $sav['address']=$request->address;
            $sav['bvn']=$request->bvn;
            $sav['acct_no'] = $request->acct_no;
            $sav['acct_name'] = $request->acct_name;
            UserBank::create($sav);
            event(new SystemLogEvent("Bank Details created", Auth::user()->id));
            return back()->with('success', 'Bank details was successfully created');
        }
    }
        
    public function submitsave(Request $request)
    {
        $user=$data['user']=User::find(Auth::user()->id);
        $date1=strtotime($request->end_date);
        $date2=strtotime(Carbon::now());
        $set= Settings::first();
        $amount=($request->amount)+($request->amount*$set->saving_charge/100);
        $loan=$data['loan']=Loan::where('user_id', Auth::user()->id)->where('status', 1)->count();
        if($loan<1){
            if($date1>$date2){
                if($user->balance>$amount || $user->balance==$amount){
                    $sav['user_id']=Auth::user()->id;
                    $sav['amount']=$request->amount;
                    $sav['target']=$request->target;
                    $sav['end_date']=$request->end_date;
                    $sav['reference']=round(microtime(true));
                    $sav['status'] = 0;
                    Save::create($sav);
                    $a=$user->balance-$amount;
                    $token=str_random(10);
                    $user->balance=$a;
                    $user->save();
                    
                    event(new SystemLogEvent("Saving initiated and completed", Auth::user()->id));
                    
                    return back()->with('success', 'Operation was succesfully, you wont have access to this funds till '.date("Y/m/d", strtotime($request->end_date)));             
                }else{
                    return back()->with('warning', 'Insufficient Funds, please fund your account');
                }
            }else{
                return back()->with('alert', 'Wrong date entered, end date must be greater than todays date');
            }
        }else{
            return back()->with('alert', 'Request failed, you have an unpaid loan.');
        }
    } 
        
    public function loansubmit(Request $request)
    {
        $set=$data['set']=Settings::first();
        $amountx=($request->amount*$set->collateral_percent)/100;
        $amountp=($request->amount*$set->loan_interest)/100;
        $amountp=$request->amount+$amountp;
        $user=$data['user']=User::find(Auth::user()->id);
        $loan=$data['loan']=Loan::where('user_id', Auth::user()->id)->where('status', 1)->count();
        if($loan<1){
            if($user->balance>$amountx || $user->balance==$amountx){
                event(new SystemLogEvent("Loan initiated successfully", Auth::user()->id));
                $sav['user_id']=Auth::user()->id;
                $sav['amount']=$amountp;
                $sav['status']=0;
                $sav['reference']=round(microtime(true));
                $sav['details']=$request->details;
                Loan::create($sav);
                event(new SystemLogEvent("Loan created successfully", Auth::user()->id));
                return back()->with('success', 'Loan proposal has been submitted, you will be updated shortly.');
            }else{
                return back()->with('alert', 'Account balance must exceed or equal to 50% of loaned amount as collateral.');
            }
        }else{
            event(new SystemLogEvent("Loan failed successfully", Auth::user()->id));
            return back()->with('alert', 'Proposal failed, you currently have an unpaid loan.');
        }
    }       
    
    public function Destroyticket($id)
    {
        $data = Ticket::findOrFail($id);
        $res =  $data->delete();
        if ($res) {
            event(new SystemLogEvent("User delete ticket request", Auth::user()->id));
            return back()->with('success', 'Request was Successfully deleted!');
        } else {
            return back()->with('alert', 'Problem With Deleting Request');
        }
    } 

    public function submitticket(Request $request)
    {
        $user=$data['user']=User::find(Auth::user()->id);
        $sav['user_id']=Auth::user()->id;
        $sav['subject']=$request->subject;
        $sav['priority']=$request->category;
        $sav['message']=$request->details;
        $sav['ticket_id']=round(microtime(true));
        $sav['status']=0;
        
        Ticket::create($sav);
        event(new SystemLogEvent("User submit ticket", Auth::user()->id));
        return back()->with('success', 'Ticket Submitted Successfully.');
    }     
    
    public function submitreply(Request $request)
    {
        $sav['reply']=$request->details;
        $sav['ticket_id']=$request->id;
        $sav['status']=1;
        Reply::create($sav);
        $data=Ticket::whereTicket_id($request->id)->first();
        $data->status=0;
        $data->save();
        event(new SystemLogEvent("User submit reply to the ticket", Auth::user()->id));
        return back()->with('success', 'Message sent!.');
    }  
    
    
    public function cardlesswithdrawsubmit(Request $request)
    {
        $set=$data['set']=Settings::first();
        $currency=Currency::whereStatus(1)->first();
        $user=$data['user']=User::find(Auth::user()->id);
        $amount=$request->amount+200;
        //$amount=$request->amount;
        $loan=$data['loan']=Loan::where('user_id', Auth::user()->id)->where('status', 1)->count();
        $ref = str_random(16);
        
        if(strtoupper($request->channel) == "POS"){
            if($request->amount < 1000 || $request->amount > 10000){
                return back()->with('alert', 'Limit amount per withdrawal for POS Channel is NGN1000 - NGN10000');
            }
        }
        
        
        if(strtoupper($request->channel) == "ATM"){
            if($request->amount < 1000 || $request->amount > 20000){
                return back()->with('alert', 'Limit amount per withdrawal for POS Channel is NGN1000 - NGN20000');
            }
        }
        
        
        
        if($loan<1){
            
            event(new SystemLogEvent("Cardless withdrawal initiated", Auth::user()->id));

            if($user->balance>$amount || $user->balance==$amount){
                
                event(new SystemLogEvent("Request sent to the server", Auth::user()->id));
                $cardless = $this->rubies->withdrawWithPayCode(strtoupper($request->channel), $ref, $request->amount, $request->pin);
            
                if($cardless['responsemessage'] == 'successful' && $cardless['responsecode'] == '00'){
                    
                    event(new SystemLogEvent("Request successfully", Auth::user()->id));
                    
                    $sav['user_id']=Auth::user()->id;
                    $sav['reference']=$cardless['paymentref'];
                    $sav['amount']=$request->amount;
                    $sav['pin']=$request->pin;
                    $sav['token']=$cardless['payWithMobileToken'];
                    $sav['channel']=$request->channel;
                    CardlessWithdraw::create($sav);
                    $a=$user->balance-($amount);
                    $user->balance=$a;
                    $user->save();
                    event(new SystemLogEvent("Debit user account, Amount: ".$amount, Auth::user()->id));
                    event(new SystemLogEvent("Alert sent", Auth::user()->id));
                    if($set->email_notify==1){
                        send_email(
                            $user->email, 
                            $user->username, 
                            'Withdrawal Token generated successfully', 
                            'Withdrawal token generated can be only used in this channel'.strtoupper($request->channel).' with this pin '.$currency->pin.'.<br>Thanks for working with us.'
                        );
                    }
                    return back()->with('success', 'Withdrawal token generated successfully');
                } else {
                     return back()->with('alert', 'Something went wrong');
                }
            }else{
                return back()->with('alert', 'Insufficent balance.');
            }
        }else{
            return back()->with('alert', 'Request failed, you have an unpaid loan.');
        }
    }
    
        
    public function withdrawsubmit(Request $request)
    {
        $set=$data['set']=Settings::first();
        $currency=Currency::whereStatus(1)->first();
        $user=$data['user']=User::find(Auth::user()->id);
        $amount=$request->amount+($request->amount*$set->withdraw_charge/100);
        $loan=$data['loan']=Loan::where('user_id', Auth::user()->id)->where('status', 1)->count();
        if($loan<1){
            if($user->balance>$amount || $user->balance==$amount){
                
                event(new SystemLogEvent("Withdrawal initiated", Auth::user()->id));
                
                $sav['user_id']=Auth::user()->id;
                $sav['reference']=str_random(16);
                $sav['amount']=$request->amount;
                $sav['status']=0;
                $sav['coin_id']=$request->coin;
                $sav['details']=$request->details;
                Withdraw::create($sav);
                $a=$user->balance-($amount);
                $user->balance=$a;
                $user->save();
                event(new SystemLogEvent("Debit User Account, Amount: ".$amount, Auth::user()->id));
                event(new SystemLogEvent("Alert Sent", Auth::user()->id));
                if($set->email_notify==1){
                    send_email(
                        $user->email, 
                        $user->username, 
                        'Withdraw Request currently being Processed', 
                        'We are currently reviewing your withdrawal request of '.$request->amount.$currency->name.'.<br>Thanks for working with us.'
                    );
                }
                return back()->with('success', 'Withdrawal request has been submitted, you will be updated shortly.');
            }else{
                return back()->with('alert', 'Insufficent balance.');
            }
        }else{
            return back()->with('alert', 'Request failed, you have an unpaid loan.');
        }
    }  
        
    public function fundsubmit(Request $request)
    {
        
        if($request->hasFile('evidence_upload')){
            
            $user=User::find(Auth::user()->id);
            $set=Settings::first();
                    
            $image = $request->file('evidence_upload');
                
            $filename = time() . '_btc_deposit_' . $user->username . '.jpg';
                    
            $location = 'asset/deposit/' . $filename;
                    
            Image::make($image)->resize(800, 800)->save($location);
            
            $charge = $set->transfer_chargex;
            $usdamo = $request->usd_amount;
            $trx = round(microtime(true));
            $depo['user_id'] = Auth::user()->id;
            $depo['gateway_id'] = "22";
            $depo['amount'] = $request->amount;
            $depo['charge'] = $charge;
            $depo['usd'] = $usdamo;
            $depo['evidence'] = $filename;
            $depo['btc_amo'] = $request->btc_amount;
            $depo['btc_wallet'] = $request->btc_wallet;
            $depo['trx'] = $trx;
            $depo['try'] = 0;
            $depo['status'] = 0;
            Deposits::create($depo);
            Session::put('Track', $depo['trx']);
            
            return redirect()->route('user.preview'); 
                    
        } else {
            return back()->with('alert', 'Please upload payment evidence');
        }
        
    }
    
    public function cryptosubmit(Request $request)
    {
        
        if($request->type == 2){
            
            if($request->hasFile('proof') && !empty($request->hash)){
                
                $user=User::find(Auth::user()->id);
                $set=Settings::first();
                        
                $image = $request->file('proof');
                    
                $filename = time() . '_exchange_crypto'.$request->currency.'_' . $user->username . '.jpg';
                        
                $location = 'asset/exchange/' . $filename;
                        
                Image::make($image)->resize(800, 800)->save($location);
                
                $charge = $set->transfer_chargex;
                $trx = round(microtime(true));
                $depo['user_id'] = Auth::user()->id;
                $depo['mode'] = $request->type;
                $depo['currency'] = $request->currency;
                $depo['amount'] = $request->amountngn;
                $depo['crypto_amount'] = $request->amountcrypto;
                $depo['usd_amount'] = $request->amountusd;
                $depo['evidence'] = $filename;
                $depo['hash'] = $request->hash;
                $depo['wallet_address'] = $request->wallet_address;
                $depo['reference'] = $trx;
                $depo['status'] = 0;
                CryptoCurrency::create($depo);
                
                return back()->with('success', 'Exchange submitted successfully, wait for confirmation');
            } else {
                return back()->with('success', 'Please upload payment evidence');
            }
                    
        } else {
            $user=User::find(Auth::user()->id);
            $set=Settings::first();
            
            $charge = $set->transfer_chargex;
            $trx = round(microtime(true));
            $depo['user_id'] = Auth::user()->id;
            $depo['mode'] = $request->type;
            $depo['currency'] = $request->currency;
            $depo['amount'] = $request->amountngn;
            $depo['crypto_amount'] = $request->amountcrypto;
            $depo['usd_amount'] = $request->amountusd;
            $depo['wallet_address'] = $request->wallet_address;
            $depo['reference'] = $trx;
            $depo['status'] = 0;
            CryptoCurrency::create($depo);
            
            return back()->with('success', 'Exchange submitted successfully, wait for confirmation');
        }
        
    }
    
    public function fundconfirm(Request $request)
    {
        
        return redirect()->route('user.fund')->with('success', 'Deposit submitted successfully, wait for confirmation');
        
    }
    public function depositpreview()
    {
        $track = Session::get('Track');
        $data['title']='Deposit Preview';
        $data['gate'] = Deposits::where('status', 0)->where('trx', $track)->first();
        return view('user.preview', $data);
    }

    public function payloan($id)
    {
        $loan=Loan::find($id);
        $user=User::where('id', $loan->user_id)->first();
        if($user->balance>$loan->amount || $user->balance==$loan->amount){
            $a=$user->balance-$loan->amount;
            $loan->status=2;
            $loan->save();
            event(new SystemLogEvent("Loan payback successfully", Auth::user()->id));
            $user->balance=$a;
            $user->save();
            event(new SystemLogEvent("Debit User Account, Amount: ".$loan->amount, Auth::user()->id));
            return back()->with('success', 'Loan was successfully paid.');
        }else{
            return back()->with('alert', 'Account balance must exceed loaned amount.');
        }
    }  
    
    public function withdrawupdate(Request $request)
    {
        $withdraw=Withdraw::whereId($request->withdraw_id)->first();
        $withdraw->coin_id=$request->coin;
        $withdraw->details=$request->details;
        $withdraw->save();
        event(new SystemLogEvent("Updated withdraw", Auth::user()->id));
        return back()->with('success', 'Successfully updated');
    }    
    
    public function calculate(Request $request)
    {
        $currency=Currency::whereStatus(1)->first();
        $plan=Plans::find($request->plan_id);
        $profit=$request->amount*($plan->percent/100)*12;
        if($request->amount>$plan->min_deposit || $request->amount==$plan->min_deposit){
            if($request->amount<$plan->amount  || $request->amount==$plan->amount){
                return back()->with('success', number_format($profit).$currency->name);  
            }else{
                return back()->with('alert', 'value is greater than maximum deposit');  
            }
        }else{
            return back()->with('alert', 'value is less than minimum deposit');  
        }
    }
    
    public function buy(Request $request)
    {
        $plan=$data['plan']=Plans::Where('id',$request->plan)->first();
        $user=User::find(Auth::user()->id);
        $loan=$data['loan']=Loan::where('user_id', Auth::user()->id)->where('status', 1)->count();
        if($loan<1){
            if($user->upgrade!=0){
                if($user->balance>$request->amount || $user->balance==$request->amount){
                    if($request->amount>$plan->min_deposit || $request->amount==$plan->min_deposit){
                        if($request->amount<$plan->amount  || $request->amount==$plan->amount){
                            $sav['user_id']=Auth::user()->id;
                            $sav['plan_id']=$request->plan;
                            $sav['amount']=$request->amount;
                            $sav['profit']=0;
                            $sav['status']=1;
                            $sav['end_date']=0;
                            $sav['date']=Carbon::now();
                            $sav['trx']=str_random(16);
                            Profits::create($sav);
                            $a=$user->balance-$request->amount;
                            $user->balance=$a;
                            $user->save();
                            return back()->with('success', 'Plan was successfully purchased, click track earnings to watch your monthly earnings');  
                        }else{
                            return back()->with('alert', 'value is greater than maximum deposit');  
                        }
                    }else{
                        return back()->with('alert', 'value is less than minimum deposit');  
                    }
                }else{
                    return back()->with('alert', 'Insufficient Funds, please fund your account');
                }
            }else{
                return back()->with('alert', 'Upgrade your account to have exclusive access to PY scheme');  
            }
        }else{
                return back()->with('alert', 'Request failed, you have an unpaid loan.');
        }

    } 

        public function read()
    {
        $alert=Alerts::where('user_id', Auth::user()->id)->get();
        foreach ($alert as $alerts){
            $alerts->seen=1;
            $alerts->save();
        }
        event(new SystemLogEvent("User read notification", Auth::user()->id));
        return back()->with('success', 'Notifications have been cleared.');
    }
        public function upgrade()
    {
        $user=User::where('id', Auth::user()->id)->first();
        $set=Settings::first();
        if($user->balance>$set->upgrade_fee || $user->balance==$set->upgrade_fee){
            $a=$user->balance-$set->upgrade_fee;
            $user->upgrade=1;
            $user->balance=$a;
            $user->save();  
            event(new SystemLogEvent("User upgrade successfully", Auth::user()->id));
            event(new SystemLogEvent("Debit User Account, Amount: ".$set->upgrade_fee, Auth::user()->id));
            return back()->with('success', 'You now have access to exclusive services.');
        }else{
            return back()->with('alert', 'Insufficient balance, add more funds..');
        }

    }

    public function logout()
    {
        event(new SystemLogEvent("Just logged out", Auth::user()->id));
        Auth::guard()->logout();
        session()->forget('fakey');
        session()->flash('message', 'Just Logged Out!');
        return redirect('/');
    }
    
    
    public function autologout()
    {
        $last_session = Session::get('varspayusersession');
        
        if($last_session !== Auth::user()->session_flag){
            
            event(new SystemLogEvent("Someone logged in your account", Auth::user()->id));
            Auth::guard()->logout();
            session()->forget('fakey');
            session()->forget('varspayusersession');
            session()->flash('message', 'Just Logged Out!');

            return "bad";
            
        } else {
            
            return "good";
            
        }
    }
    
    
    public function submitPin(Request $request)
    {
        $this->validate($request, [
            'current_pin' => 'required',
            'pin' => 'required|max:4|confirmed'
        ]);
        try {

            $c_pin = Auth::user()->pin;
            $c_id = Auth::user()->id;
            $user = User::findOrFail($c_id);
            if ($request->current_pin==$c_pin) {
                if($request->pin==$request->pin_confirmation){
                    $user->pin = $request->pin;
                    $user->save();
                    event(new SystemLogEvent("User changed pin", Auth::user()->id));
                    return back()->with('success', 'Pin Changed Successfully.');
                }else{
                    return back()->with('alert', 'New Pin Does Not Match.');
                }
            } else {
                return back()->with('alert', 'Current Pin Not Match.');
            }

        } catch (\PDOException $e) {
            return back()->with('alert', $e->getMessage());
        }
    }
    
    public function submitPassword(Request $request)
    {
        $this->validate($request, [
            'current_password' => 'required',
            'password' => 'required|min:5|confirmed'
        ]);
        try {

            $c_password = Auth::user()->password;
            $c_id = Auth::user()->id;
            $user = User::findOrFail($c_id);
            if (Hash::check($request->current_password, $c_password)) {
                if($request->password==$request->password_confirmation){
                    $password = Hash::make($request->password);
                    $user->password = $password;
                    $user->save();
                    event(new SystemLogEvent("User changed password", Auth::user()->id));
                    return back()->with('success', 'Password Changed Successfully.');
                }else{
                    return back()->with('alert', 'New Password Does Not Match.');
                }
            } else {
                return back()->with('alert', 'Current Password Not Match.');
            }

        } catch (\PDOException $e) {
            return back()->with('alert', $e->getMessage());
        }
    }
    
        public function avatar(Request $request)
    {
        $user = User::findOrFail(Auth::user()->id);
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . $user->username . '.jpg';
            $location = 'asset/profile/' . $filename;
            if ($user->image != 'user-default.png') {
                $path = './asset/profile/';
                File::delete($path.$user->image);
            }
            Image::make($image)->resize(800, 800)->save($location);
            $user->image=$filename;
            $user->save();
            event(new SystemLogEvent("User changed profile picture", Auth::user()->id));
            return back()->with('success', 'Avatar Updated Successfully.');
        }else{
            return back()->with('success', 'An error occured, try again.');
        }
    }
    
    public function bvnVerify(Request $request){
        
        $curl = curl_init();
        
        $set=Settings::first();
  
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/bank/resolve_bvn/".$request->bvn."",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
              "Authorization: Bearer ".$set->paystack_secretkey."",
              "Cache-Control: no-cache",
            ),
        ));
          
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
          
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            echo $response;
        }
    }
    public function posFormCreate(Request $request)
    {
        $posform = PosForm::where('user_id', $request->user_id)->get();
        $posformlogic = $posform->count();
        if($posformlogic == 1){
            
            $id = $posform->get()->id;
            $timeSubmitted = $posform->get()->time_submitted;
            $passport = $posform->get()->passport;
            $fullname = $posform->get()->fullname;
            $age = $posform->get()->age;
            $bvn = $posform->get()->bvn;
            $address = $posform->get()->address;
            $account = $posform->get()->account;
            $phone = $posform->get()->phone;
            
            if($timeSubmitted < 3){
                
                $posformupdate = PosForm::find($id);
                
                $timeSubmitted = $timeSubmitted + 1;
                
                if($request->hasFile('passport')){
                    $image = $request->file('passport');
                    $filename = time() . '_' . $request->bvn . '.jpg';
                    $location = 'asset/posform/' . $filename;
                    if ($passport) {
                        $path = './asset/posform/';
                        $link = $path . $passport;
                        if (file_exists($link)) {
                            @unlink($link);
                        }
                    }
                    Image::make($image)->save($location);
                    
                }
                
                if(!empty($request->age) || !empty($request->fullname) || !empty($request->email) || !empty($request->account) || !empty($request->bvn) || !empty($request->address) || !empty($request->phone)){
                    
                    $posformupdate->user_id = $request->user_id;
                    $posformupdate->passport = $filename;
                    $posformupdate->fullname = $request->fullname;
                    $posformupdate->bvn = $request->bvn;
                    $posformupdate->email = $request->email;
                    $posformupdate->address = $request->address;
                    $posformupdate->age = $request->age;
                    $posformupdate->phone = $request->phone;
                    $posformupdate->account = $request->account;
                    $posformupdate->status = 0;
                    $posformupdate->time_submitted = $timeSubmitted;
                    
                    $postformupdate->save();
                
                    return back()->with('success', 'Your form has been successfully updated, we will get in touch with you later');
                    
                } else {
                    
                    $posformupdate->user_id = $request->user_id;
                    $posformupdate->fullname = $fullname;
                    $posformupdate->passport = $passport;
                    $posformupdate->bvn = $bvn;
                    $posformupdate->email = $email;
                    $posformupdate->address = $address;
                    $posformupdate->age = $age;
                    $posformupdate->phone = $phone;
                    $posformupdate->account = $account;
                    $posformupdate->status = 0;
                    $posformupdate->time_submitted = $timeSubmitted;
                    
                    if($postformupdate->save()){
                        $message = "Your POS Agent Form has been updated, we will get in touch with you once approved by Admin";
                        send_email($posformupdate->email, $posformupdate->fullname, 'POS Agent Form Updated', $message);
                        
                        return back()->with('success', 'Your form has been successfully updated, we will get in touch with you later');
                    }
                    
                }
                
                
            } else {
                
                return back()->with('success', 'You have submitted this form 3 times, contact the admin for help');
                
            }
            
            
        } else {
            
            if($request->hasFile('passport') && !empty($request->age) && !empty($request->fullname) && !empty($request->email) && !empty($request->account) && !empty($request->bvn) && !empty($request->address) && !empty($request->phone)){
                
                $image = $request->file('passport');
                $filename = time() . '_' . $request->bvn . '.jpg';
                $location = 'asset/posform/' . $filename;
                Image::make($image)->save($location);
                
                $posformdata = new PosForm();
                $posformdata->user_id = $request->user_id;
                $posformdata->fullname = $request->fullname;
                $posformdata->email = $request->email;
                $posformdata->address = $request->address;
                $posformdata->account = $request->account;
                $posformdata->phone = $request->phone;
                $posformdata->bvn = $request->bvn;
                $posformdata->age = $request->age;
                $posformdata->passport = $filename;
                $posformdata->status = 0;
                $posformdata->time_submitted = 1;
                
                if($posformdata->save()){
                    $message = "Your POS Agent Form has been submitted successfully, we will get in touch with you once approved by Admin";
                    send_email($posformdata->email, $posformdata->fullname, 'POS Agent Form Submit Successfully', $message);
                    
                    return back()->with('success', 'Successfully submitted, we will get back to you shortly');
                }
                
                
            } else {
                
                return back()->with('success', 'Please fill all form data');
            }
            
        }
    }
    
        public function kyc(Request $request)
    {
        $user = User::findOrFail(Auth::user()->id);
        $set=Settings::first();
        if ($request->hasFile('kyc_identity') && $request->hasFile('kyc_selfie')) {
            
            if($user->balance>50 || $user->balance==50){
            
                $b=$user->balance-50;
                $user->balance=$b;
                $user->save();
                $token=str_random(10);
                $content='Acct:'.$user->acct_no.', Date:'.Carbon::now().', DR Amt: 50,
                                Bal:'.$user->balance.', Ref:'.$token.', Desc: KYC Verification';
                $debit['user_id']=Auth::user()->id;
                $debit['amount']=50;
                $debit['details']=$content;
                $debit['type']=1;
                $debit['seen']=0;
                $debit['status']=1;
                $debit['reference']=$token;
                Alerts::create($debit);
                if($set->sms_notify==1){
                    send_sms($user->phone, $content);
                }
                if($set['email_notify']==1){
                    send_alert_email($user->email, $user->username, 'Debit Alert', 'DR', $user->acct_no, $token, 50, 'Debit Alert', $user->balance, Carbon::now());
                }
            
                $identity = $request->file('kyc_identity');
                $selfie = $request->file('kyc_selfie');
                
                $kyc_identity = time() . '_identity_' . $user->username . '.jpg';
                $kyc_selfie = time() . '_selfie_' . $user->username . '.jpg';
                
                $identity_location = 'asset/kyc/' . $kyc_identity;
                $selfie_location = 'asset/kyc/' . $kyc_selfie;
                
                if ($user->kyc_link != 'user-default.png') {
                    $path = './asset/kyc/';
                    $link = $path . $user->kyc_link;
                    if (file_exists($link)) {
                        @unlink($link);
                    }
                }
                
                if ($user->kyc_selfie != 'user-default.png') {
                    $path = './asset/kyc/';
                    $link = $path . $user->kyc_selfie;
                    if (file_exists($link)) {
                        @unlink($link);
                    }
                }
                
                Image::make($identity)->resize(800, 800)->save($identity_location);
                Image::make($selfie)->resize(800, 800)->save($selfie_location);
                
                if($request->hasFile('kyc_cac')){
                    
                    $cac = $request->file('kyc_cac');
                
                    $kyc_cac = time() . '_cac_' . $user->username . '.jpg';
                    
                    $cac_location = 'asset/kyc/' . $kyc_cac;
                    
                    if ($user->kyc_cac != 'user-default.png') {
                        $path = './asset/kyc/';
                        $link = $path . $user->kyc_cac;
                        if (file_exists($link)) {
                            @unlink($link);
                        }
                    }
                    
                    Image::make($cac)->resize(800, 800)->save($cac_location);
                    
                    $user->kyc_cac = $kyc_cac;
                    
                }
                
                $user->kyc_link = $kyc_identity;
                $user->kyc_selfie = $kyc_selfie;
                $user->kyc_bvn=$request->kyc_bvn;
                $user->kyc_email=$request->kyc_email;
                $user->kyc_phone=$request->kyc_phone;
                $user->save();
                event(new SystemLogEvent("User submit kyc documents", Auth::user()->id));
                return back()->with('success', 'Kyc document Upload was successful.');
            } else {
                return back()->with('alert', 'Insufficient Balance, this service cost NGN50');
            }
        }else{
            return back()->with('success', 'An error occured, try again.');
        }
    }
        public function account(Request $request)
    {
        $user = User::findOrFail(Auth::user()->id);
        $user->name=$request->name;
        $user->phone=$request->phone;
        $user->country=$request->country;
        $user->bvn=$request->bvn;
        $user->btc_address=$request->btc_address;
        $user->eth_address=$request->eth_address;
        $user->business=$request->business;
        $user->city=$request->city;
        $user->zip_code=$request->zip_code;
        $user->address=$request->address;
        $user->save();
        event(new SystemLogEvent("User updated profile", Auth::user()->id));
        return back()->with('success', 'Profile Updated Successfully.');
    }

    public function submitbuyasset(Request $request)
    {
        $user=$data['user']=User::find(Auth::user()->id);
        $currency=Currency::whereStatus(1)->first();
        $plan=Chart::whereId($request->asset)->first();
        $set=Settings::first();
        $rate=(($request->amount*$plan->price)*(100-$plan->buying_charge)/100);
        $token = str_random(16);
        $loan=$data['loan']=Loan::where('user_id', Auth::user()->id)->where('status', 1)->count();
        if($loan<1){
            if($request->amount<$user->balance || $request->amount==$user->balance){
                if($rate<$plan->balance || $rate==$plan->balance){
                    $num_asset=Asset::where('user_id', Auth::user()->id)->where('plan_id', $request->asset)->count();
                    if($num_asset<1){
                        $sav['user_id']=Auth::user()->id;
                        $sav['plan_id']=$request->asset;
                        $sav['amount']=$rate;
                        Asset::create($sav);
                    }else{
                        $up_asset=Asset::where('user_id', Auth::user()->id)->where('plan_id', $request->asset)->first();
                        $up_asset->amount=$up_asset->amount+$rate;
                        $up_asset->save();
                    }
                    $user->balance=$user->balance-$request->amount;
                    $user->save();
                    $plan->balance=$plan->balance-$rate;
                    $plan->save();
                    $buyer['reference']=$token;
                    $buyer['user_id']=Auth::user()->id;
                    $buyer['plan_id']=$request->asset;
                    $buyer['amount']=$rate;
                    $buyer['charge']=$request->amount*$plan->price*$plan->buying_charge/100;
                    Buyer::create($buyer);
                    return back()->with('success', $plan->name.' was successfully purchased');
                }else{
                    return back()->with('alert', 'Reserved currency is below amount entered');
                }
            }else{
                return back()->with('alert', 'Account balance is insufficient');
            }
        }else{
            return back()->with('alert', 'Request failed, you have an unpaid loan.');
        }

    }      
    
    public function submitsellasset(Request $request)
    {
        $user=$data['user']=User::find(Auth::user()->id);
        $currency=Currency::whereStatus(1)->first();
        $plan=Chart::whereId($request->asset)->first();
        $set=Settings::first();
        $token = str_random(16);
        $rate=(($request->amount/$plan->price)*(100+$plan->selling_charge)/100);
        $stock=Asset::where('user_id', Auth::user()->id)->where('plan_id', $request->asset)->first();
        $sell=($request->amount*$plan->selling_charge/100)+$request->amount;
        if($request->amount<$stock->amount || $request->amount==$stock->amount){
            $stock->amount=$stock->amount-$sell;
            $stock->save();
            $user->balance=$user->balance+$rate;
            $user->save();            
            $plan->balance=$plan->balance+$request->amount;
            $plan->save();
            $seller['reference']=$token;
            $seller['user_id']=Auth::user()->id;
            $seller['plan_id']=$request->asset;
            $seller['amount']=$sell;
            $seller['charge']=$request->amount*$plan->selling_charge/100;
            Seller::create($seller);
            event(new SystemLogEvent($plan->name.' was successfully sold', Auth::user()->id));
            return back()->with('success', $plan->name.' was successfully sold');
        }else{
            return back()->with('alert', 'Account balance is insufficient');
        }        
    }      
    
    public function submitexchangeasset(Request $request)
    {
        $user=$data['user']=User::find(Auth::user()->id);
        $currency=Currency::whereStatus(1)->first();
        $set=Settings::first();
        if($request->from==$request->to){
            return back()->with('alert', 'You cannot exchange thesame asset.');
        }else{
            event(new SystemLogEvent("Exchange initiated", Auth::user()->id));
            $stock=Asset::where('user_id', Auth::user()->id)->where('plan_id', $request->from)->first();
            if($request->amount<$stock->amount || $request->amount==$stock->amount){
                Session::put('Amount', $request->amount);
                Session::put('From', $request->from);
                Session::put('To', $request->to);
                return redirect()->route('user.checkasset');  
            }else{
                return back()->with('alert', 'Account balance is insufficient');
            }
        }       
    } 

    public function submitcheckasset(Request $request)
    {
        $user=$data['user']=User::find(Auth::user()->id);
        $currency=Currency::whereStatus(1)->first();
        $plan=Chart::whereId($request->from)->first();
        $set=Settings::first();
        $token = str_random(16);
        $num_asset=Asset::where('user_id', Auth::user()->id)->where('plan_id', $request->to)->count();
        if($num_asset<1){
            $sav['user_id']=Auth::user()->id;
            $sav['plan_id']=$request->to;
            $sav['amount']=$request->tamount;
            Asset::create($sav);
        }else{
            $to_asset=Asset::where('user_id', Auth::user()->id)->where('plan_id', $request->to)->first();
            $to_asset->amount=$to_asset->amount+$request->tamount;
            $to_asset->save();            
            $from_asset=Asset::where('user_id', Auth::user()->id)->where('plan_id', $request->from)->first();
            $from_asset->amount=$from_asset->amount-$request->famount;
            $from_asset->save();
        }
        $exchange['reference']=$token;
        $exchange['user_id']=Auth::user()->id;
        $exchange['famount']=$request->famount;
        $exchange['tamount']=$request->tamount;
        $exchange['frome']=$request->from;
        $exchange['toe']=$request->to;
        $exchange['charge']=$request->from*$plan->exchange_charge/100;
        Exchange::create($exchange);
        event(new SystemLogEvent("Exchange successfully", Auth::user()->id));
        return redirect()->route('user.exchangeasset')->with('success', 'Exchange was successful');      
    } 
        
    public function submittransferasset(Request $request)
    {
        $set=$data['set']=Settings::first();
        $user=$data['user']=User::find(Auth::user()->id);
        $kex=User::whereAcct_no($request->acct_no)->get();
        $amount=$request->amount+$request->amount*$set->transfer_charge/100;
        if(count($kex)>0){
            $stock=Asset::where('user_id', Auth::user()->id)->where('plan_id', $request->asset)->first();
            if($amount<$stock->amount || $amount==$stock->amount){
                $receiver=User::whereAcct_no($request->acct_no)->first();
                if($user->pin==$request->pin){
                    if($user->id!=$receiver->id){
                        $sav['sender_id']=Auth::user()->id;
                        $sav['receiver_id']=$receiver->id;
                        $sav['amount']=$request->amount;
                        $sav['asset']=$request->asset;
                        $sav['ref_id']=str_random(16);
                        Assettransfer::create($sav);
                        $stock->amount=$stock->amount-($request->amount+$request->amount*$set->transfer_charge/100);
                        $stock->save();     
                        $num_asset=Asset::where('user_id', $receiver->id)->where('plan_id', $request->asset)->count();
                        if($num_asset<1){
                            $savx['user_id']=$receiver->id;
                            $savx['plan_id']=$request->asset;
                            $savx['amount']=$request->amount;
                            Asset::create($savx);
                        }else{
                            $to_asset=Asset::where('user_id', $receiver->id)->where('plan_id', $request->asset)->first();
                            $to_asset->amount=$to_asset->amount+$request->amount;
                            $to_asset->save();            
                        }    
                        event(new SystemLogEvent("User transfer asset successfully", Auth::user()->id));
                        return back()->with('success', 'Transaction successful.');
                    }else{
                        return back()->with('alert', 'Invalid account number.');
                    }
                }else{
                    return back()->with('alert', 'Invalid pin.');
                }
            }else{
                return back()->with('alert', 'Insufficent balance.');
            }
        }else{
            return back()->with('alert', 'Invalid account number.');
        }
    } 

    public function updatemerchant(Request $request)
    {
        $data = Merchant::find($request->id);
        $in = Input::except('_token');
        if($request->hasFile('image')){
            $image = $request->file('image');
            $filename = 'merchant_'.time().'.png';
            $location = 'asset/profile/' . $filename;
            Image::make($image)->save($location);
            $path = './asset/profile/';
            File::delete($path.$data->image);
            $in['image'] = $filename;
        }
        $res = $data->fill($in)->save();
        event(new SystemLogEvent("User update merchant", Auth::user()->id));
        if ($res) {
            return back()->with('success', 'Saved Successfully!');
        } else {
            return back()->with('alert', 'Problem With updating merchant');
        }
    } 

    public function transferprocess()
    {
        $data['title'] = "Make payment";
        $data['id']= $id = request('id');
        $data['token']= $token = request('token');
        $data['ext']=Exttransfer::whereReference($token)->first();
        $data['merchant']=Merchant::whereMerchant_key($id)->first();
        event(new SystemLogEvent("Checked in to make payment", Auth::user()->id));
        return view('user.transfer_process', $data);
    }    
    
    public function Cancelmerchant()
    {
        $data['id']= $id = request('id');
        $ext=Exttransfer::whereReference($id)->first();
        $ext->status=2;
        $ext->save();
        event(new SystemLogEvent("User cancel merchant", Auth::user()->id));
        return Redirect::away($ext->fail_url);
    }    
    
    public function Paymerchant()
    {
        $data['id']= $id = request('id');
        $set=Settings::first();
        $ext=Exttransfer::whereReference($id)->first();
        $debit=User::whereId($ext->user_id)->first();
        $amount=$ext->amount+($ext->amount*$set->merchant_charge/100);
        if($amount<$debit->balance || $amount==$debit->balance){
            $ext->status=1;
            $ext->save();
            $merchant=Merchant::whereMerchant_key($ext->merchant_key)->first();
            $up_mer=User::whereId($merchant->user_id)->first();
            $up_mer->balance=$ext->amount+$up_mer->balance;
            $up_mer->save();
            $debit->balance=$debit->balance-($ext->amount+($ext->amount*$set->merchant_charge/100));
            $debit->save();
            return Redirect::away($ext->notify_url);
        }else{
            return back()->with('alert', 'Account balance is insufficient');
        }     
    }

    public function submitpay(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $count=Merchant::whereMerchant_key($request->merchant_key)->whereStatus(1)->get();
        $token = str_random(16);
        if(count($count)>0){
            $data['merchant']=$merchant=Merchant::whereMerchant_key($request->merchant_key)->whereStatus(1)->first();
            if($merchant->user_id!=Auth::user()->id){
                $mer['reference']=$token;
                $mer['user_id']=Auth::user()->id;
                $mer['amount']=$request->amount;
                $mer['merchant_key']=$request->merchant_key;
                $mer['success_url']=$request->success_url;
                $mer['fail_url']=$request->fail_url;
                $mer['notify_url']=$request->notify_url;
                $mer['status']=0;
                Exttransfer::create($mer);
                return redirect()->route('transfer.process', ['id'=>$request->merchant_key, 'token'=>$token]);
            }else{
                return redirect()->route('user.dashboard')->with('alert', 'Access denied');
            }
        }else{
            return redirect()->route('user.dashboard')->with('alert', 'Invalid merchant key');
        }

    }

    public function submit2fa(Request $request)
    {
        $user = User::findOrFail(Auth::user()->id);
        $g=new \Sonata\GoogleAuthenticator\GoogleAuthenticator();
        $secret=$request->vv;
        if ($request->type==0) {
            $check=$g->checkcode($secret, $request->code, 3);
            if($check){
                $user->fa_status = 0;
                $user->googlefa_secret = null;
                $user->save();
                return back()->with('success', '2fa disabled.');
            }else{
                return back()->with('alert', 'Invalid code.');
            }
        }else{
            $check=$g->checkcode($secret, $request->code, 3);
            if($check){
                $user->fa_status = 1;
                $user->googlefa_secret = $request->vv;
                $user->save();
                return back()->with('success', '2fa enabled.');
            }else{
                return back()->with('alert', 'Invalid code.');
            }
        }
    }

    public function sellgift()
    {

        $data['title'] = "Exchange Giftcard";
        $data['giftcard'] = GiftCard::whereStatus(1)->orderBy('name','asc')->get();

       return view('user.giftcard', $data);
       
    }


    public function sellgift2($id)
    {

        $get['title'] = "Exchange Giftcard";
        $get['gcard'] = GiftCard::whereId($id)->first();
        $get['gctype'] = GiftCardType::whereStatus(1)->whereCard_id($id)->orderBy('name','asc')->get();

       return view('user.giftcard-select', $get);
    }


    public function excard(Request $request)
    {

     $this->validate($request,
            [
            'card' => 'required',
            'type' => 'required',
            'amount' => 'required',
            'type' => 'required',
            ]);
       $card = GiftCard::whereId($request->card)->first();
       
       if($request->type == 1){
           $this->validate($request, [
            'front' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'back' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            ]);
        }
        
        if($request->type == 2){
            $this->validate($request, [
             'code' => 'required',
            ]);
        }
        
        $type = GiftCardType::whereId($request->cardtype)->first();

        $get = $request->amount * $type->rate;

        $docm['user_id'] = Auth::id();
        $docm['type'] = $request->cardtype;
        $docm['card_id'] = $request->card;
        $docm['currency'] = $request->type;
        $docm['amount'] = $request->amount;
        $docm['country'] = $type->currency;
        $docm['rate'] = $type->rate;
        $docm['pay'] = $get;
        $docm['status'] = 0;
        $docm['trx'] = strtoupper(Str::random(6));
        if($request->code){
            $docm['code'] = $request->code;
        }
        if($request->hasFile('front')){
            $this->validate($request, [
                'front' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            ]);
            $docm['image'] = uniqid().'.jpg';
            $request->front->move('giftcards',$docm['image']);
            
            
            $image = $request->file('front');
            $docm['image'] = time() . '_front_' . $user->username . '.jpg';
            $location = 'asset/giftcards/' . $filename;
            Image::make($image)->resize(800, 800)->save($location);
        }
        if($request->hasFile('back')){
            $this->validate($request, [
                'back' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            ]);
            
            $image = $request->file('back');
            $docm['image2'] = time() . '_back_' . $user->username . '.jpg';
            $location = 'asset/giftcards/' . $filename;
            Image::make($image)->resize(800, 800)->save($location);
        }
        GiftCardSale::create($docm);

        return back()->with('success', 'Giftcard exchange successfully');
    }


    public function excardlog(){

        $auth = Auth::user();
        $get['title'] = "Giftcard Log";
        $get['gcard'] = GiftCardSale::whereUser_id($auth->id)->orderBy('created_at','desc')->get();

       return view('user.giftcard-log', $get);
    }


    public function exchangeCrypto()
    {

        $data['title'] = "Exchange CryptoCurrency";
        $url='https://bitpay.com/api/rates';
        $json=json_decode( file_get_contents( $url ) );
        $usd = 1;
        $btc = 0;
        $eth = 0;
        foreach( $json as $obj ){
            if( $obj->code=='USD' ){
                $btc = $obj->rate;
                
            }
            if( $obj->code=='ETH' ){
                $eth = $obj->rate;
                
            }
            
        }
        $baseUrl = "https://api.coingate.com";
		$endpoint = "/v2/rates/merchant/USD/NGN";
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

        $data['usdrate'] = json_decode(curl_exec( $ch ),true);
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        curl_close($ch);
        $data['ethrate'] = ($usd/$eth)*$btc;
        $data['btcrate'] = $btc;
        $data['crypto']=CryptoCurrency::where('user_id', Auth::user()->id)->latest()->get();

       return view('user.cryptocurrency', $data);
       
    }
}
