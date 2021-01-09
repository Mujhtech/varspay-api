<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

use App\Models\User;
use App\Models\Merchant\User as MerchantUser;
use App\Models\Merchant\Role as MerchantRole;
use App\Models\Merchant\Wallet as MerchantWallet;
use App\Models\Merchant\RoleUser as MerchantRoleUser;
use App\Models\Merchant\Permission as MerchantPermission;
use App\Models\Merchant\Setting as MerchantSetting;
use App\Models\Merchant\Transaction as MerchantTransaction;
use App\Models\Merchant\PermissionRole as MerchantPermissionRole;
use App\Models\Merchant\RequestPayment as MerchantRequestPayment;
use App\Models\Merchant\UserDetail as MerchantUserDetail;
use App\Models\Merchant\Transfer as MerchantTransfer;
use App\Models\Settings;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Lib\Providus;
use Auth;

class RegisterController extends Controller
{

    protected $redirectTo = '/user/dashboard';
    
    public $providus;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
        $this->providus = new Providus;
       
    }

    public function register()
    {
		$data['title']='Register';
		if(Auth::user()){
			return redirect()->intended('user/dashboard');
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
        
        
        $generateAcct = $this->providus->generateAccount($codeRef,$request->name, $request->email);
        
        if($generateAcct["requestSuccessful"] && $generateAcct["responseBody"]["status"] == "ACTIVE"){
            
            $acct = $generateAcct["responseBody"]["accountNumber"];
            
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
        $user->phone_verify = $phone_verify;
        $user->phone_time = $phone_time;
        $user->balance = $basic->balance_reg;
        $user->ip_address = user_ip();
        $user->acct_no = $acct;
        $user->acct_ref = $codeRef;
        $user->pin = '0000';
        $user->password = Hash::make($request->password);
        $user->save();

        
        
        $merchantuser  = new MerchantUser();
        $merchantuser->first_name = $request->name;
        $merchantuser->last_name = $request->username;
        $merchantuser->email      = $request->email;
        $merchantuser->type       = "merchant";
        $merchantuser->phone          = $request->phone;
        $merchantuser->defaultCountry = "ng";
        $merchantuser->carrierCode    = "234";
        $merchantuser->password = Hash::make($request->password);
        
        $role = MerchantRole::select('id')->where(['customer_type' => 'merchant', 'user_type' => 'User'])->first();
        
        $checkPermission = MerchantPermission::where(['user_type' => 'User'])->get(['id']); //checkPermission
        if (!empty($checkPermission))
        {
            foreach ($checkPermission as $cp)
            {
                $checkPermissionRole = MerchantPermissionRole::where(['permission_id' => $cp->id, 'role_id' => $role->id]); //checkPermissionRole
                if (!empty($checkPermissionRole))
                {
                    MerchantPermissionRole::firstOrCreate(['permission_id' => $cp->id, 'role_id' => $role->id]);
                }
            }
        }
        
        $merchantuser->role_id = $role->id;
        $merchantuser->save();
        
        MerchantRoleUser::insert(['user_id' => $merchantuser->id, 'role_id' => $role->id, 'user_type' => 'User']); // Assigning user type and role to new user
        
        $UserDetail          = new MerchantUserDetail();
        
        $UserDetail->user_id = $merchantuser->id;
        $timezone            = MerchantSetting::where('name', 'default_timezone')->first();
        $UserDetail->country_id = "154";
        $UserDetail->timezone = $timezone->value;
        $UserDetail->save();
        
        
        $default_currency = MerchantSetting::where('name', 'default_currency')->first(['value']); // default_currency

        // Wallet creation
        $wallet              = new MerchantWallet();
        $wallet->user_id     = $merchantuser->id;
        $wallet->currency_id = $default_currency->value;
        $wallet->balance     = 0.00;
        $wallet->is_default  = 'Yes';
        $wallet->save();

                /**
                 * Entry for unknown transfer
                 */
        $unknownTransferTransaction = MerchantTransaction::where([
            'email' => $merchantuser->email,
            'user_type' => 'unregistered'])->whereIn('transaction_type_id', ["3", "4"])->select('transaction_reference_id', 'email')->get();
        if ($unknownTransferTransaction)
        {
            foreach ($unknownTransferTransaction as $key => $value)
            {
                $transfer = MerchantTransfer::where([
                    'receiver_id' => null,
                    'id'          => $value->transaction_reference_id,
                    'email'       => $value->email,
                ])->select('amount', 'currency_id')->first();

                if (isset($transfer))
                {
                    $transferInstance              = MerchantTransfer::find($value->transaction_reference_id);
                    $transferInstance->receiver_id = $merchantuser->id;
                    $transferInstance->status      = 'Success';
                    $transferInstance->save();

                    MerchantTransaction::where([ 
                        'transaction_reference_id' => $value->transaction_reference_id,
                        'transaction_type_id' => Transferred, ])->update([
                            'end_user_id' => $merchantuser->id,
                            'user_type'   => 'registered',
                            'status'      => 'Success',
                    ]);

                    MerchantTransaction::where([
                        'transaction_reference_id' => $value->transaction_reference_id,
                        'transaction_type_id'      => Received,
                        ])->update([
                            'user_id'   => $merchantuser->id,
                            'user_type' => 'registered',
                            'status'    => 'Success',
                    ]);
                    
                    $unknownTransferWallet = MerchantWallet::where(['user_id' => $merchantuser->id, 'currency_id' => $transfer->currency_id])->first();
                    
                    if (empty($unknownTransferWallet))
                    {
                        $wallet              = new MerchantWallet();
                        $wallet->user_id     = $merchantuser->id;
                        $wallet->currency_id = $transfer->currency_id;

                        if ($wallet->currency_id == $default_currency->value)
                        {
                            $wallet->is_default = 'Yes';
                        }
                        else
                        {
                            $wallet->is_default = 'No';
                        }
                        $wallet->balance = $transfer->amount;
                        $wallet->save();
                    }
                    else
                    {
                    $wallet              = MerchantWallet::find($unknownTransferWallet->id);
                    $wallet->user_id     = $merchantuser->id;
                    $wallet->currency_id = $transfer->currency_id;
                    $wallet->balance     = $unknownTransferWallet->balance + $transfer->amount;
                    $wallet->save();
                    }
                }
            }
        }
        
        /**
                 * Entry for unknown request payment
                 */
        $unknownRequestTransaction = MerchantTransaction::where([
            'email' => $merchantuser->email,
            'user_type' => 'unregistered'])->whereIn('transaction_type_id', ["9", "10"])->select('transaction_reference_id', 'email')->get();

        if ($unknownRequestTransaction)
        {
            foreach ($unknownRequestTransaction as $key => $value)
            {
                $request_payment = RequestPayment::where([
                    'receiver_id' => null,
                    'id'          => $value->transaction_reference_id,
                    'email'       => $value->email,
                ])->select('currency_id')->first();

                if (isset($request_payment))
                {
                    $request_paymentInstance              = MerchantRequestPayment::find($value->transaction_reference_id);
                    $request_paymentInstance->receiver_id = $merchantuser->id;
                    $request_paymentInstance->save();
                    
                    MerchantTransaction::where([
                        'transaction_reference_id' => $value->transaction_reference_id,
                        'transaction_type_id'      => Request_From,
                    ])->update([
                        'end_user_id' => $merchantuser->id,
                        'user_type'   => 'registered',
                    ]);

                    MerchantTransaction::where([
                        'transaction_reference_id' => $value->transaction_reference_id,
                        'transaction_type_id'      => Request_To,
                    ])->update([
                        'user_id'   => $merchantuser->id,
                        'user_type' => 'registered',
                    ]);

                    $unknownRequestWallet = MerchantWallet::where(['user_id' => $merchantuser->id, 'currency_id' => $request_payment->currency_id])->first();

                    if (empty($unknownRequestWallet))
                    {
                        $wallet              = new MerchantWallet();
                        $wallet->user_id     = $merchantuser->id;
                        $wallet->currency_id = $request_payment->currency_id;

                        if ($wallet->currency_id == $default_currency->value)
                        {
                            $wallet->is_default = 'Yes';
                        }
                        else
                        {
                            $wallet->is_default = 'No';
                        }
                        $wallet->balance = 0.00;
                        $wallet->save();
                    }
                    else
                    {
                        $wallet              = MerchantWallet::find($unknownRequestWallet->id);
                        $wallet->user_id     = $merchantuser->id;
                        $wallet->currency_id = $request_payment->currency_id;
                        if ($wallet->currency_id == $default_currency->value)
                        {
                        $wallet->is_default = 'Yes';
                        }
                        else
                        {
                            $wallet->is_default = 'No';
                        }
                        $wallet->save();
                    }
                }
            }
        }
        
        if ($basic->email_verification == 1) {
            $text = "Your Email Verification Code Is: <b>$user->verification_code</b>";
            send_email($user->email, $user->name, 'Email verification', $text);
        }
        if ($basic->sms_verification == 1) {
            $message = "Your phone verification code is: $user->sms_code";
            send_sms($user->phone, strip_tags($message));
        }

        if (Auth::attempt([
            'username' => $request->username,
            'password' => $request->password,
        ])) {

            return redirect()->intended('user/dashboard');
        }
    }
}
