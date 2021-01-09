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
use App\Http\Resources\InternetCollectionResource;
use App\Http\Resources\DataPlanCollectionResource;
use App\Events\ApiSystemLogEvent;




class TransferController extends Controller
{
    
    public $providus;
    
    public $api_key;
    
    public $rubies;
    
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
    
    public function own(Request $request){
        
        $set=Settings::first();
        $amountx=$request->input('amount')+$set->transfer_charge;
        $token = round(microtime(true));
        $user=$data['user']=User::find($this->authorized_user);
        $loan=$data['loan']=Loan::where('user_id', $this->authorized_user)->where('status', 1)->count();
        if($loan<1){
            if($user->email!=$request->input('email')){
                    $count=User::whereEmail($request->input('email'))->get();
                    if(count($count)>0){
                        $trans=User::where('email', $request->input('email'))->first();
                        if($user->balance>$amountx || $user->balance==$amountx){
                            
                            $a=$trans->balance+$request->input('amount');
                            $b=$user->balance-$amountx;
                            $trans->balance=$a;
                            $trans->save(); 
                            $user->balance=$b;
                            $user->save();
                            $sav['ref_id']=$token;
                            $sav['amount']=$request->input('amount');
                            $sav['sender_id']=$this->authorized_user;
                            $sav['receiver_id']=$trans->id;
                            $sav['status']=1;
                            $sav['type']=1;
                            Transfer::create($sav);
                            $contentx='Acct:'.$trans->acct_no.', Date:'.Carbon::now().', CR Amt:'.$request->input('amount').',
                            Bal:'.$trans->balance.', Ref:'.$token.', Desc: Bank transfer';
                            $credit['user_id']=$trans->id;
                            $credit['amount']=$request->input('amount');
                            $credit['details']=$contentx;
                            $credit['type']=2;
                            $credit['seen']=0;
                            $credit['status']=1;
                            $credit['reference']=$token;
                            Alerts::create($credit);
                            
                            if($set->sms_notify==1){
                                send_sms($trans->phone, $contentx);
                            }    
                            if($set['email_notify']==1){
                                
                                send_alert_email($trans->email, $trans->username, 'Credit Alert', 'CR', $trans->acct_no, $token, $request->input('amount'), 'Credit Alert', $trans->balance, Carbon::now());
                            }
                            $content='Acct:'.$user->acct_no.', Date:'.Carbon::now().', DR Amt:'.$request->input('amount').',
                            Bal:'.$user->balance.', Ref:'.$token.', Desc: Bank transfer';
                            $debit['user_id']=$this->authorized_user;
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
                                send_alert_email($user->email, $user->username, 'Debit Alert', 'DR', $user->acct_no, $token, $request->input('amount'), 'Debit Alert', $user->balance, Carbon::now());
                            }
                            event(new ApiSystemLogEvent("Transfer completed", $this->authorized_user));
                            return response()->json([
                                'responseMessage' => 'Transfer completed successfully',
                                'responseCode' => 200
                            ], 200);
                             
                        }else{
                            return response()->json([
                                'responseMessage' => 'Insufficient Balance',
                                'responseCode' => 200
                            ], 200);
                        }
                    }else{
                        return response()->json([
                            'responseMessage' => 'Invalid Email Address',
                            'responseCode' => 500
                        ], 500);
                    }
            }else{
                return response()->json([
                        'responseMessage' => 'You cant transfer money to the same account.',
                        'responseCode' => 500
                    ], 500);
            }
        }else{
            return response()->json([
                'responseMessage' => 'Request failed, you have an unpaid loan',
                'responseCode' => 500
            ], 500);  
        }

        return response()->json([
            'responseMessage' => 'Resource not found.',
            'responseCode' => 404
        ], 404);
        
    }
    
    
    public function other(Request $request)
    {
        $set=Settings::first();
        $amountx=$request->input('amount') +$set->api_charges;
        $token = round(microtime(true));
        $user=$data['user']=User::find($this->authorized_user);
        $loan=$data['loan']=Loan::where('user_id', $this->authorized_user)->where('status', 1)->count();
        if($loan<1){
                if($user->acct_no!=$request->input('accountNumber')){
                    if($user->balance>$amountx || $user->balance==$amountx){
                        
                        
                        $b=$user->balance-$amountx;
                            $user->balance=$b;
                            $user->save();
                            
                            $inttransfer = new Int_transfer;
                            $inttransfer->details='Acct name:'.$request->input('accountName').', Bank name:'.$this->rubies->bankName($request->input('bankCode'));
                            $inttransfer->ref_id=$token;
                            $inttransfer->amount=$amountx;
                            $inttransfer->acct_no=$request->input('accountNumber');
                            $inttransfer->bank_name=$this->rubies->bankName($request->input('bankCode'));
                            $inttransfer->user_id=$this->authorized_user;
                            $inttransfer->status=0;
                            $inttransfer->type=1;
                            $inttransfer->save();
                            
                            
                        $transfer = $this->rubies->singleTransfer(
                            $request->input('amount'),
                            $request->input('description'),
                            $this->rubies->acctName($request->input('accountNumber'),$request->input('bankCode')),
                            $this->rubies->bankName($request->input('bankCode')),
                            $set->site_name,
                            $request->input('accountNumber'),
                            $request->input('bankCode'),
                            $token);
                            
                        if($transfer["responsecode"] == "00" && $transfer["responsemessage"] == "Success"){
                               
                            
                            $inttransfer->status=1;
                            $inttransfer->sessionid = $transfer['sessionid'];
                            $inttransfer->save();
                            
                            event(new ApiSystemLogEvent("Transfer completed", $this->authorized_user));
                            $content='Acct:'.$user->acct_no.', Date:'.Carbon::now().', DR Amt:'.$amountx.',
                                Bal:'.$user->balance.', Ref:'.$token.', Desc: Bank transfer';
                            $debit['user_id']=$this->authorized_user;
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
                                
                            return response()->json([
                                'reference' => $token,
                                'responseMessage' => 'Transfer completed successfully',
                                'responseCode' => 200
                            ], 200);
                            
                        } else {
                            return response()->json([
                                'responseMessage' => $transfer["responsemessage"],
                                'responseCode' => 500
                            ], 500);
                        }
    
                    } else {
                        return response()->json([
                            'responseMessage' => 'Insufficient Balance',
                            'responseCode' => 500
                        ], 500);
                    }
                } else {
                    return response()->json([
                        'responseMessage' => 'You cant transfer money to the same account.',
                        'responseCode' => 500
                    ], 500);
                }
        }else{
                return response()->json([
                    'responseMessage' => 'Request failed, you have an unpaid loan.',
                    'responseCode' => 500
                ], 500);     
            }

        
        return response()->json([
            'responseMessage' => 'Resource not found.',
            'responseCode' => 404
        ], 404);
    }
    
    public function bulkCsv(Request $request)
    {  
        
        if (!is_array($request->input('bulkList'))) {    
            
            return response()->json(['responseCode' => 500, 'responseMessage'=>'An array of bulk transfer required'], 500);                        
        }
        
        if(!empty($request->input('bulkList'))){
            
            $ar = [];
            $token = round(microtime(true));
            $set=Settings::first();
            for($i = 0; $i < count($request->input('bulkList')); $i++){
                
                $token = $token.$i;
                $ar[$i]["amount"] = $request->input('bulkList')[$i]['amount'];
                $ar[$i]["craccount"] = $request->input('bulkList')[$i]['accountnumber'];
                $ar[$i]["bankcode"] = $request->input('bulkList')[$i]['bankcode'];
                $ar[$i]["draccountname"] = $set->site_name;
                $ar[$i]["narration"] = $request->input('bulkList')[$i]['narration'];
                $ar[$i]["craccountname"] = $this->rubies->acctName($request->input('bulkList')[$i]['accountnumber'],$request->input('bulkList')[$i]['bankcode']);
                $ar[$i]["bankname"] = $this->rubies->bankName($request->input('bulkList')[$i]['bankcode']);
                $ar[$i]["reference"] = $token;
                
            }
            
            $totalAmount = 0;
            
            for($i = 0; $i < count($ar); $i++){
                $totalAmount += intval($ar[$i]["amount"]);
            }
            
            $set=Settings::first();
            $amountx=$totalAmount+($set->api_charges * count($new_ar));
            $token = round(microtime(true));
            $user=$data['user']=User::find($this->authorized_user);
            $loan=$data['loan']=Loan::where('user_id', $this->authorized_user)->where('status', 1)->count();
            if($loan<1){
                if($user->acct_no!= $request->acct_no){
                        if($user->balance>$amountx || $user->balance==$amountx){
                            
                            $batch = new BatchTransfer;
                            $batch->title = $request->name;
                            $batch->reference = "batch".round(microtime(true));
                            $batch->status = 0;
                            $batch->user_id=$this->authorized_user;
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
                                $sav['bankname']=$v['bankname'];
                                $sav['user_id']=$this->authorized_user;
                                BatchTransferList::create($sav);
                                
                                $sav['details']='Acct Name:'.$v["craccountname"].', Bank name:'.$v["bankname"];
                                $sav['ref_id']=$v["reference"];
                                $sav['amount']=$v["amount"];
                                $sav['bulk_id'] = $request->id;
                                $sav['acct_no']=$v["craccount"];
                                $sav['bank_name']=$v["bankname"];
                                $sav['bank_code']=$v["bankcode"];
                                $sav['narration']=$v["narration"];
                                $sav['acct_name']=$v["craccountname"];
                                $sav['user_id']=$this->authorized_user;
                                $sav['status']=0;
                                $sav['type']=1;
                                Int_transfer::create($sav);
                                
                            }
                            
                            
                            $validation = $this->rubies->bulkTransferValidation($batch->reference, $ar);
                            
                            if($validation['responsecode'] == "00" && $validation['totalvalidtransactions'] == $validation['totalbatch']){
                            
                                $transfer = $this->rubies->bulkTransfer($batch->reference, $ar);
                                
                                if($transfer["responsecode"] == "00"){
                                    
                                    $batch = BatchTransfer::where('reference', $batch->reference)->first();
                                    $batch->amount = $amountx;
                                    $batch->status = 3;
                                    $batch->save();
                                    
                                    $user = User::find($batch->user_id);
                                    $b = $user->balance - $amountx;
                                    $user->balance = $b;
                                    $user->save();
                                    event(new ApiSystemLogEvent("Transfer queued for processing", $this->authorized_user));
                                    return response()->json([
                                        'responseMessage' => 'Bulk transfer queued for processing',
                                        'batchReference' => $batch->reference,
                                        'responseCode' => 200
                                    ], 200);
                                
                                } else {
                                    return response()->json([
                                        'responseMessage' => 'Something went wrong',
                                        'batchReference' => $batch->reference,
                                        'responseCode' => 500
                                    ], 500);
                                }
                                
                            } else {
                                
                                return response()->json([
                                    'responseMessage' => $validation['totalvalidtransactions'] ." valid out of ".$validation['totalbatch']." transaction",
                                    'responseCode' => 500
                                ], 500);
                            }
                            
                        } else {
                            
                            return response()->json([
                                'responseMessage' => 'Insufficient Balance',
                                'responseCode' => 500
                            ], 500);
                        }
                } else {
                        
                        return response()->json([
                            'responseMessage' => 'You cant transfer money to the same account.',
                            'responseCode' => 500
                        ], 500);
                    }
 
            } else {
                
                return response()->json([
                    'responseMessage' => 'Request failed, you have an unpaid loan',
                    'responseCode' => 500
                ], 500);
                
            }
            
        } else {
            
            return response()->json([
                'responseMessage' => 'Bulk list shouldnt be empty',
                'responseCode' => 500
            ], 500);
         
        }
        
        return response()->json([
            'responseMessage' => 'Resource not found',
            'responseCode' => 404
        ], 404);
    }
    
    
    public function otherBulkDetails(Request $request){
        
        $batch = BatchTransfer::where('reference', $request->query('batchReference'))->first();
            
        if($batch->status == 1){
            event(new ApiSystemLogEvent("Query bulk transfer transaction", $this->authorized_user));
            $batch_list = Int_transfer::where('bulk_id', $batch->id)->get();
            $ar = [];
                
            for($i = 0; $i < count($batch_list); $i++){
                
                $ar[$i]["amount"] = $batch_list[$i]->amount;
                $ar[$i]["narration"] = $batch_list[$i]->narration;
                $ar[$i]["account"] = $batch_list[$i]->acct_no;
                $ar[$i]["accountname"] = $batch_list[$i]->acct_name;
                $ar[$i]["bankname"] = $batch_list[$i]->bank_name;
                $ar[$i]["bankcode"] = $batch_list[$i]->bank_code;
                $ar[$i]["reference"] = $batch_list[$i]->ref_id;
                $ar[$i]["status"] = $batch_list[$i]->status ? 'successful' : 'pending';
                            
            }
            return response()->json([
                'batchReference' => $request->query('batchReference'),
                'batchStatus' => 'success',
                'totalAmount' => $batch->amount,
                'totalTransaction' => count(Int_transfer::where("bulk_id", $batch->id)->get()),
                'transactions' => $ar,
                'responseMessage' => 'Transfer successfully sent',
                'responseCode' => 200
            ], 200);
            
        } else {
            
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
                
                $set=Settings::first();
                $user=$data['user']=User::find($batch->user_id);
                $b=$user->balance-$batch->amount;
                $user->balance=$b;
                $user->save();
                            
                $content='Acct:'.$user->acct_no.', Date:'.Carbon::now().', DR Amt:'.$batch->amount.',
                Bal:'.$user->balance.', Ref:'.$batch->reference.', Desc: Bulk transfer';
                $debit['user_id']=$this->authorized_user;
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
                
                $batch_list = Int_transfer::where('bulk_id', $batch->id)->get();
                $ar = [];
                
                for($i = 0; $i < count($batch_list); $i++){
                
                    $ar[$i]["amount"] = $batch_list[$i]->amount;
                    $ar[$i]["narration"] = $batch_list[$i]->narration;
                    $ar[$i]["account"] = $batch_list[$i]->acct_no;
                    $ar[$i]["accountname"] = $batch_list[$i]->acct_name;
                    $ar[$i]["bankname"] = $batch_list[$i]->bank_name;
                    $ar[$i]["bankcode"] = $batch_list[$i]->bank_code;
                    $ar[$i]["reference"] = $batch_list[$i]->ref_id;
                    $ar[$i]["status"] = $batch_list[$i]->status ? 'successful' : 'pending';
                            
                }
                        
                        
                return response()->json([
                    'batchReference' => $request->query('batchReference'),
                    'batchStatus' => 'success',
                    'totalAmount' => $batchs->amount,
                    'totalTransaction' => count(Int_transfer::where("bulk_id", $batch->id)->get()),
                    'transactions' => $ar,
                    'responseMessage' => 'Transfer successfully sent',
                    'responseCode' => 200
                ], 200);
            }
            
        }
        
        return response()->json([
            'responseMessage' => 'Resource not found',
            'responseCode' => 404
        ], 404);
        
    }
    
    
    
    public function otherSingleDetails(Request $request){
        
        $single = Int_transfer::where('ref_id', $request->query('reference'))->first();
            
        if($single->status == 1){
            event(new ApiSystemLogEvent("Query single transfer transaction", $this->authorized_user));
            return response()->json([
                'reference' => $request->query('reference'),
                'status' => 'success',
                'accountname' => $single->acct_name,
                'accountnumber' => $single->acct_no,
                'bankname' => $single->bank_name,
                'bankcode' => $single->bank_code,
                'amount' => $single->amount,
                'narration' => $single->narration,
                'responseMessage' => 'Transfer successfully sent',
                'responseCode' => 200
            ], 200);
            
        }
        
        return response()->json([
            'responseMessage' => 'Resource not found',
            'responseCode' => 404
        ], 404);
        
    }
    
    
    
    
    
}
