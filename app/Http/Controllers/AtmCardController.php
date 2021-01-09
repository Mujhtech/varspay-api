<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\CreditCard;
use App\Models\User;
use Carbon\Carbon;


class AtmCardController extends Controller
{

    public function index()
    {
        $data['title']='Atm Card';
        $data['atmcard']=CreditCard::all();
        return view('admin.atmcard.index', $data);
    }
    
    public function accept($id){
        
        $atmcard = CreditCard::find($id);
        
        $user=$data['user']=User::find($atmcard->user_id);
        
        $atmcard->status = 1;
        
        if($atmcard->save()){
            $message = "Your Atm Card has been activated";
            send_email($user->email, $user->username, 'ATM Card Activation', $message);
            
            return back()->with('success', 'ATM Card Approved');
        }
        
    } 
    
    public function delete($id){
        
        $atmcard = CreditCard::find($id);
        
        $user=$data['user']=User::find($atmcard->user_id);
        
        if($atmcard->delete()){
            $message = "Your Atm Card has been delete";
            send_email($user->email, $user->username, 'ATM Card Deleted', $message);
            
            return back()->with('success', 'ATM Card Deleted');
        }
        
    } 
    
    
}
