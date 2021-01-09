<?php

use Illuminate\Support\Facades\Route;
use App\Lib\Providus;
use App\Lib\Rubies;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|bank_transfersubmit
*/

Route::get('/testing', function(){
     
    $rubies = new Rubies();
    $providus = new Providus();
    
    print_r($rubies->bulkTransferDetails("batch1604064311"));
    //print_r($providus->generateAccount("anilgibee063257", "Mom", "alegenibi2020@gmail.com"));
    //print_r($providus->generateAccount("mk12e3i062539", "Mike", "mike123@gmail.com"));
    //print_r($providus->generateAccount("aaoaokldha024845", "Alaka", "busayoalaka@gmail.com"));
    //print_r($providus->checkAccountDetails("aoaokldha024845"));
       
});
//Webhook ROute
Route::group(['prefix' => 'webhook', ], function () {
    Route::post('/providus/bulk_transfer', 'UserController@webhookBulkTransfer');
    Route::post('/rubies/bulk_transfer', 'UserController@webhookBulkRubiesConfirm')->name('webhookRubiesBulkTransfer');
    
    Route::post('/providus/wallet_payment', 'UserController@webhookWalletPayment');
    Route::post('/providus/bulk_payment_confirmation', 'UserController@webhookBulkConfirm')->name('webhookBulkConfirm');
    Route::post('/providus/transfer_payment_confirmation', 'UserController@webhookWalletTransfer')->name('webhookWalletTransfer');
    Route::post('/rubies/transfer_payment_confirmation', 'UserController@webhookRubiesWalletTransfer')->name('webhookRubiesWalletTransfer');
    Route::get('/providus/fetch_account/{bank}/{acct_no}', function($bank, $acct_no){
        
        $providus = new Providus();
        
        $acct_name = $providus->verifyAcctNo($acct_no,$bank);
        
        if($acct_name["requestSuccessful"] && $acct_name["responseMessage"] == "success"){
            
            echo $acct_name["responseBody"]["accountName"];
            
        } else {
            
            echo "error";
            
        }
        
    })->name('webhookFetchAccount');
    Route::get('/rubies/fetch_account/{bank}/{acct_no}', function($bank, $acct_no){
        
        $rubies = new Rubies();
        
        $acct_name = $rubies->nameEnquries($acct_no,$bank);
        
        if($acct_name["responsecode"] == 00 && $acct_name["responsemessage"] == "success"){
            
            echo $acct_name["accountname"];
            
        } else {
            
            echo "error";
            
        }
        
    })->name('webhookRubiesFetchAccount');
});

//IPN Route
Route::get('/ipnbtc', 'PaymentController@ipnBchain')->name('ipn.bchain');
Route::post('/ipnpaypal', 'PaymentController@ipnpaypal')->name('ipn.paypal');
Route::post('/ipnperfect', 'PaymentController@ipnperfect')->name('ipn.perfect');
Route::post('/ipnstripe', 'PaymentController@ipnstripe')->name('ipn.stripe');
Route::post('/ipnskrill', 'PaymentController@skrillIPN')->name('ipn.skrill');
Route::post('/ipnflutter', 'PaymentController@flutterIPN')->name('ipn.flutter');
Route::post('/ipnvogue', 'PaymentController@vogueIPN')->name('ipn.vogue');
Route::post('/ipnpaystack', 'PaymentController@paystackIPN')->name('ipn.paystack');
Route::post('/ipncoinpaybtc', 'PaymentController@ipnCoinPayBtc')->name('ipn.coinPay.btc');
Route::post('/ext_transfer', 'UserController@submitpay')->name('submit.pay');

//Frontend Route
//Route::get('/home', 'FrontendController@index')->name('home');
Route::get('/faq', 'FrontendController@faq')->name('faq');
Route::get('/about', 'FrontendController@about')->name('about');
Route::get('/blog', 'FrontendController@blog')->name('blog');
Route::get('/terms', 'FrontendController@terms')->name('terms');
Route::get('/kyc', 'FrontendController@kyc')->name('kyc');
Route::get('/careers', 'FrontendController@careers')->name('careers');
Route::get('/privacy', 'FrontendController@privacy')->name('privacy');
Route::get('/page/{id}', 'FrontendController@page');
Route::get('/single/{id}/{slug}', 'FrontendController@article');
Route::get('/cat/{id}/{slug}', 'FrontendController@category');
Route::get('/contact', 'FrontendController@contact')->name('contact');
Route::post('/contact', ['uses' => 'FrontendController@contactSubmit', 'as' => 'contact-submit']);
Route::post('/subscribe', 'FrontendController@subscribe')->name('subscribe');
Route::post('/py_scheme', 'FrontendController@py_scheme')->name('py_scheme');;

//Route Login
Route::get('/', 'LoginController@login')->name('login');
Route::post('/login', 'LoginController@submitlogin')->name('submitlogin');

//Two factor verification Route
Route::get('/2fa', 'LoginController@faverify')->name('2fa');
Route::post('/2fa', 'LoginController@submitfa')->name('submitfa');

//Registration route
Route::get('/register', 'RegisterController@register')->name('register');
Route::post('/register', 'RegisterController@submitregister')->name('submitregister');

//Forget password route
Route::get('/forget', 'UserController@forget')->name('forget');
Route::get('/r_pass', 'UserController@r_pass')->name('r_pass');

//Auto Logout
Route::get('/autologout', 'UserController@autologout')->name('autologout');

//Password reset route
Route::get('user-password/reset', 'User\ForgotPasswordController@showLinkRequestForm')->name('user.password.request');
Route::get('user-password/reset/{token}', 'User\ResetPasswordController@showResetForm')->name('user.password.reset');
Route::post('user-password/reset', 'User\ResetPasswordController@reset')->name('user.password.reset');;
Route::post('user-password/email', 'User\ForgotPasswordController@sendResetLinkEmail')->name('user.password.email');

Route::get('admin-password/reset', 'Admin\ForgotPasswordController@showLinkRequestForm')->name('admin.password.request');
Route::get('admin-password/reset/{token}', 'Admin\ResetPasswordController@showResetForm')->name('admin.password.reset');
Route::post('admin-password/reset', 'Admin\ResetPasswordController@reset')->name('admin.password.reset');;
Route::post('admin-password/email', 'Admin\ForgotPasswordController@sendResetLinkEmail')->name('admin.password.email');

//User Routes
//Route::group(['prefix' => 'user', ], function () {
    Route::get('authorization', 'UserController@authCheck')->name('user.authorization');   
    Route::post('verification', 'UserController@sendVcode')->name('user.send-vcode');
    Route::post('smsVerify', 'UserController@smsVerify')->name('user.sms-verify');
    Route::post('verify-email', 'UserController@sendEmailVcode')->name('user.send-emailVcode');
    Route::post('postEmailVerify', 'UserController@postEmailVerify')->name('user.email-verify'); 
        Route::group(['middleware'=>'isActive'], function() {
            Route::middleware(['CheckStatus'])->group(function () {
                Route::get('dashboard', 'UserController@dashboard')->name('user.dashboard');
                
                Route::get('bankcode', 'UserController@bankcode')->name('user.bankcode');
                
                //Voucher Route GET
                Route::get('voucher-airtimetopup', 'UserController@airtimeTop')->name('user.airtimetop');
                Route::get('voucher-airtimeswap', 'UserController@airtimeSwap')->name('user.airtimeswap');
                Route::get('voucher-databundle', 'UserController@dataBundle')->name('user.databundle');
                Route::get('voucher-smedata', 'UserController@smeData')->name('user.smedata');
                Route::get('voucher-tvsub', 'UserController@tvSub')->name('user.tvsub');
                Route::get('voucher-power', 'UserController@power')->name('user.power');
                Route::get('voucher-internet', 'UserController@internet')->name('user.internet');
                Route::get('voucher-epin', 'UserController@ePin')->name('user.epin');
                Route::get('myepin', 'UserController@myePin')->name('user.myepin');
                Route::get('epin-fetch', 'UserController@ePinFetch')->name('user.epinfetch');
                
                //Voucher Ajax Route
                Route::post('ajax_databundle', 'UserController@ajaxDatabundle');
                Route::post('ajax_smedatabundle', 'UserController@ajaxSmeDatabundle');
                Route::post('ajax_power', 'UserController@ajaxPower');
                Route::post('ajax_decoder', 'UserController@ajaxDecoder');
                Route::post('ajax_decoder_amount', 'UserController@ajaxDecoderAmount');
                Route::post('ajax_data_bundle_amount', 'UserController@ajaxDataBundleAmount');
                Route::post('ajax_airtimeswap', 'UserController@ajaxAirtimeSwap')->name('user.ajax.airtime');
                Route::post('ajax_epin', 'UserController@ajaxEpinQuantity')->name('user.ajax.epin');


                //Giftcard Route
                Route::get('/exchange-giftcard', 'UserController@sellgift')->name('user.exchange.giftcard');
                Route::get('/select-gift-card/{id}', 'UserController@sellgift2')->name('user.selectgiftcard');
                Route::get('/giftcard-log', 'UserController@excardlog')->name('user.excardlog');
                Route::post('/exchange-giftcard', 'UserController@excard')->name('user.excard');

                //Cryptocurrency route
                Route::get('/exchange-cryptocurrency', 'UserController@exchangeCrypto')->name('user.exchange.crypto');
                
                
                //Voucher ROute POST
                Route::post('airtime-topup-submit', 'UserController@airtimetopSubmitRubies')->name('user.airtimetopup.submit');
                Route::post('data-bundle-submit', 'UserController@databundleSubmit');
                Route::post('tvsub-submit', 'UserController@tvsubSubmit');
                Route::post('power-submit', 'UserController@powerSubmit');
                Route::post('epin-submit', 'UserController@epinSubmitNew')->name('user.epin.submit');
                
                //ATM CARD
                Route::get('atm-card', 'UserController@atmcard')->name('user.atmcard');
                Route::get('activate-atm-card/{id}', 'UserController@activateAtmcard');
                Route::get('deactivate-atm-card/{id}', 'UserController@deactivateAtmcard');
                Route::post('request-amt-card', 'UserController@requestAtmcard');
                
                
                Route::get('plans', 'UserController@plans')->name('user.plans');
                Route::post('calculate', 'UserController@calculate');
                Route::post('buy', 'UserController@buy');
                Route::post('withdraw-update', 'UserController@withdrawupdate');
                Route::get('profile', 'UserController@profile')->name('user.profile');
                Route::post('kyc', 'UserController@kyc')->name('user.kyc.upload');
                Route::post('account', 'UserController@account')->name('user.profile.update');
                Route::post('avatar', 'UserController@avatar')->name('user.avatar.update');
                Route::get('/invoicepdf/{id}', 'FrontendController@generateInvoice')->name('user.generateinvoice');
                Route::get('statement', 'UserController@statement')->name('user.statement');
                Route::get('merchant', 'UserController@merchant')->name('user.merchant');
                Route::get('sender-log', 'UserController@senderlog')->name('user.senderlog');
                Route::get('add-merchant', 'UserController@addmerchant')->name('user.add-merchant');
                Route::get('merchant-documentation', 'UserController@merchant_documentation')->name('user.merchant-documentation');
                Route::post('add-merchant', 'UserController@submitmerchant')->name('submit.merchant');
                Route::get('transfer-process/{id}/{token}', 'UserController@transferprocess')->name('transfer.process');
                Route::get('edit-merchant/{id}', 'UserController@Editmerchant')->name('edit.merchant');
                Route::get('log-merchant/{id}', 'UserController@Logmerchant')->name('log.merchant');
                Route::get('cancel-merchant/{id}', 'UserController@Cancelmerchant')->name('cancel.merchant');
                Route::get('submit-merchant/{id}', 'UserController@Paymerchant')->name('pay.merchant');
                Route::post('editmerchant', 'UserController@updatemerchant')->name('update.merchant');
                Route::get('ticket', 'UserController@ticket')->name('user.ticket');
                Route::post('submit-ticket', 'UserController@submitticket')->name('submit-ticket');
                Route::get('ticket/delete/{id}', 'UserController@Destroyticket')->name('ticket.delete');
                Route::get('reply-ticket/{id}', 'UserController@Replyticket')->name('ticket.reply');
                Route::post('reply-ticket', 'UserController@submitreply');
                Route::get('own-bank', 'UserController@ownbank')->name('user.ownbank');
                Route::post('own_bank', 'UserController@submitownbank')->name('submit.ownbank');
                Route::post('other_bank', 'UserController@submitotherbankrubies')->name('submit.otherbank');
                Route::post('bulk-transfer-create-csv', 'UserController@submitbulktransferRubiesCsv')->name('submit.bulktransfercsv');
                Route::post('bulk-transfer-create-list', 'UserController@submitbulktransferRubiesList')->name('submit.bulktransferlist');
                Route::get('other-bank', 'UserController@otherbank')->name('user.otherbank');
                Route::get('bulk-transfer', 'UserController@bulkTransfer')->name('user.bulktransfer');
                Route::get('bulk-transfers/{id}', 'UserController@bulkTransferTransaction')->name('user.bulktransfertransaction');
                Route::get('bulk-transfers/delete/{id}', 'UserController@bulkTransferDelete')->name('user.bulktransferdelete');
                
                Route::post('bulk-transfers/add-recipient', 'UserController@bulkTransferAdd')->name('user.bulktransferadd');
                Route::get('bulk-transfer/add_list', 'UserController@bulkTransferList')->name('user.bulktransfer.list');
                
                Route::post('bulk-transfer/make-payment', 'UserController@bulkTransferPost')->name('user.bulktransfer.post');
                
                Route::post('getbvnverify', 'UserController@bvnVerify');
                Route::post('local_preview', 'UserController@submitlocalpreview')->name('submit.localpreview');
                Route::get('local-preview', 'UserController@localpreview')->name('user.localpreview');
                Route::get('fund', 'UserController@fund')->name('user.fund');
                Route::get('transfer-transaction', 'UserController@transfertransact')->name('user.transfer.transaction');
                Route::get('pos-form', 'UserController@posForm')->name('user.pos.form');
                Route::post('pos-form-create', 'UserController@posFormCreate');
                Route::get('preview', 'UserController@depositpreview')->name('user.preview');
                Route::post('fund', 'UserController@fundsubmit')->name('fund.submit');
                
                Route::post('cryptocurrency', 'UserController@cryptosubmit')->name('crypto.submit');
                
                Route::post('fund-confirm', 'UserController@fundconfirm')->name('fund.confirm');
                Route::get('bank-transfer', 'UserController@bank_transfer')->name('user.bank_transfer');
                Route::get('card-payment', 'UserController@card_payment')->name('user.card_payment');
                Route::post('bank_transfer', 'UserController@bank_transfersubmit')->name('bank_transfersubmit');
                Route::post('card_payment_post', 'UserController@card_transfersubmit')->name('card_transfersubmit');
                Route::get('withdraw', 'UserController@withdraw')->name('user.withdraw');
                Route::get('cardless-withdraw', 'UserController@cardlessWithdraw')->name('user.cardless.withdraw');
                Route::post('withdraw', 'UserController@withdrawsubmit')->name('withdraw.submit');
                Route::post('cardless-withdraw', 'UserController@cardlesswithdrawsubmit')->name('cardlesswithdraw.submit');
                Route::get('save', 'UserController@save')->name('user.save');
                Route::post('save', 'UserController@submitsave')->name('submitsave');
                Route::get('branch', 'UserController@branch')->name('user.branch');
                Route::get('password', 'UserController@changePassword')->name('user.password');
                Route::post('password', 'UserController@submitPassword')->name('change.password');
                Route::get('developer', 'UserController@developer')->name('user.developer');
                Route::post('generate-api-key', 'UserController@submitApiKey')->name('submitApiKey');
                Route::get('pin', 'UserController@changePin')->name('user.pin');
                Route::post('pin', 'UserController@submitPin')->name('change.pin');
                Route::get('loan', 'UserController@loan')->name('user.loan');
                Route::post('loansubmit', 'UserController@loansubmit');
                Route::post('bankupdate', 'UserController@bankupdate');
                Route::get('payloan/{id}', 'UserController@payloan')->name('user.payloan');
                Route::get('upgrade', 'UserController@upgrade')->name('user.upgrade');
                Route::get('read', 'UserController@read')->name('user.read');
                Route::post('deposit-confirm', 'PaymentController@depositConfirm')->name('deposit.confirm');
                Route::get('buy-asset', 'UserController@buyasset')->name('user.buyasset');
                Route::post('buy_asset', 'UserController@submitbuyasset')->name('submit.buyasset');                
                Route::get('sell-asset', 'UserController@sellasset')->name('user.sellasset');
                Route::post('sell_asset', 'UserController@submitsellasset')->name('submit.sellasset');               
                Route::get('exchange-asset', 'UserController@exchangeasset')->name('user.exchangeasset');
                Route::post('exchange_asset', 'UserController@submitexchangeasset')->name('submit.exchangeasset');                    
                Route::get('transfer-asset', 'UserController@transferasset')->name('user.transferasset');
                Route::post('transfer_asset', 'UserController@submittransferasset')->name('submit.transferasset');                
                Route::get('check-asset', 'UserController@checkasset')->name('user.checkasset');
                Route::post('check_asset', 'UserController@submitcheckasset')->name('submit.checkasset');
                Route::post('2fa', 'UserController@submit2fa')->name('change.2fa');
            });
        });
    Route::get('logout', 'UserController@logout')->name('user.logout');
//});

