<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\AirtimeSwap;
use App\Models\Alerts;
use App\Models\Settings;
use App\Models\User;
use Carbon\Carbon;


class AirtimeSwapController extends Controller
{

    public function index()
    {
        $data['title']='Airtime Swap';
        $data['airtimeswap']= AirtimeSwap::all();
        return view('admin.airtimeswap.index', $data);
    }
    
    public function accept($id){
        
        $airtimeswap = AirtimeSwap::find($id);
        
        $set= Settings::first();
        
        $user_id = $airtimeswap->user_id;
        $amount = $airtimeswap->amount_to_receive;
        
        $user=$data['user']=User::find($user_id);
        
        $b = $user->balance + $amount;
        $user->balance = $b;
        
        $airtimeswap->status = 1;
        
        if($airtimeswap->save() && $user->save()){
            
            $token = round(microtime(true));
            $content='Acct:'.$user->acct_no.', Date:'.Carbon::now().', CR Amt:'.$amount.',
                Bal:'.$user->balance.', Ref:'.$token.', Desc: Airtime Swap';
            $credit['user_id']=$user_id;
            $credit['amount']=$amount;
            $credit['details']=$content;
            $credit['type']=1;
            $credit['seen']=0;
            $credit['status']=1;
            $credit['reference']=$token;
            Alerts::create($credit);
                    
            if($set->sms_notify==1){
                
                send_sms($user->phone, $content);
                
            }
            
                    
            send_email($user->email, $user->username, 'Airtime Swap Creditted', $content);
            
            return back()->with('success', 'Airtime Swap approved successfully');
        }
        
    } 
    
    public function reject($id){
        
        $airtimeswap = AirtimeSwap::find($id);
        
        if($airtimeswap->delete()){
            
            return back()->with('success', 'AirtimeSwap Deleted');
        }
        
    } 
    
}
