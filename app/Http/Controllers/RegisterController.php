<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Events\SystemLogEvent;
use App\Models\User;
use App\Models\Settings;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Lib\Providus;
use App\Lib\Rubies;
use Auth;
use Session;

class RegisterController extends Controller
{

    protected $redirectTo = '/dashboard';
    
    public $providus;
    
    public $rubies;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
        $this->providus = new Providus;
        $this->rubies = new Rubies;
    }

    public function register()
    {
		$data['title']='Register';
		if(Auth::user()){
			return redirect()->intended('dashboard');
		}else{
	        return view('/auth/register', $data);
		}
    }


    public function submitregister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|min:5|unique:users|regex:/^\S*$/u',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|numeric|min:8|unique:users',
            'password' => 'required|string|min:4',
            'checkbox' => 'accepted',
        ]);
        if ($validator->fails()) {
            // adding an extra field 'error'...
            $data['title']='Register';
            $data['errors']=$validator->errors();
            return view('/auth/register', $data);
        }

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
        
        
        $generateAcct = $this->rubies->generateVirtualAccount($request->name);
        
       // $generateAcctProvidus = $this->providus->generateAccount($codeRef,$request->name, $request->email);
        
        if($generateAcct["responsecode"] == "00" && $generateAcct["responsemessage"] == "ACCOUNT OPENED SUCCESSFULLY"){
            
            $acct = $generateAcct["virtualaccount"];
            //$acct_providus = $generateAcctProvidus["responseBody"]["accountNumber"];
            
        } else {
            
            return back()->with('alert', "Something happened, try again");
            
        }
        
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->username = $request->username;
        $user->email_verify = $email_verify;
        $user->verification_code = $verification_code;
        $user->sms_code = $sms_code;
        $user->email_time = $email_time;
        $user->level = 1;
        $user->phone_verify = $phone_verify;
        $user->phone_time = $phone_time;
        $user->balance = $basic->balance_reg;
        $user->ip_address = user_ip();
        $user->acct_no = $acct;
        //$user->providus_acct_no = $acct_providus;
        $user->acct_ref = $codeRef;
        $user->pin = '0000';
        $user->password = Hash::make($request->password);
        $new_sessid = \Session::getId();
        $user->session_flag = $new_sessid;
        $user->save();
        
        Session::put('varspayusersession', $new_sessid);
        
        if ($basic->email_verification == 1) {
            $text = "Your Email Verification Code Is: <b>$user->verification_code</b>";
            send_email($user->email, $user->name, 'Email verification', $text);
        }
        if ($basic->sms_verification == 1) {
            $message = "Your phone verification code is: $user->sms_code";
            send_sms($user->phone, strip_tags($message));
        }
       // event(new SystemLogEvent("Just joined us", $user->id));
        
        if (Auth::attempt([
            'username' => $request->username,
            'password' => $request->password,
        ])) {

            return redirect()->intended('dashboard');
        }
    }
}
