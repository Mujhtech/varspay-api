<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::get('/', function(){
    return response()->json([
        'responseMessage' => 'Server up and running',
        'responseCode' => 200
    ], 200);
});
Route::group(['namespace' => 'API', 'prefix' => 'v1', 'middleware' => 'apiauth'], function () {
    
    Route::get('/', function(){
        return response()->json([
            'responseMessage' => 'Server up and running',
            'responseCode' => 200
        ], 200);
    });
    
    //User Route
    Route::post('me', 'UserController@index');
    Route::post('user/nuban', 'UserController@nuban');
    Route::post('user/balance', 'UserController@balance');
    Route::post('user/pin', 'UserController@pin');
    Route::post('user/password', 'UserController@password');
    Route::post('user/new', 'UserController@createUser');
    
    //Virtual Account
    Route::post('virtualaccount', 'VirtualAccountController@index');
    Route::post('createvirtualaccount', 'VirtualAccountController@createAccount');
    Route::post('virtualaccount/listtransactions', 'VirtualAccountController@listAllTran');
    
    
    
    //Transaction Route
    Route::get('alerts', 'UserController@alerts');
    Route::get('banks', 'BankController@index');
    Route::post('resolve-account', 'BankController@resolveAccount');
    Route::post('resolve-bvn', 'BankController@resolveBvn');
    
    //Airtime Route
    Route::post('airtime/buy', 'VoucherController@airtimeBuyRubies');
    Route::post('airtime/query', 'VoucherController@airtimeQueries');
    Route::post('airtime/providers', 'VoucherController@airtimeProvider');
    Route::post('airtime/swap', 'VoucherController@airtimeSwap');
    Route::post('airtime/swap/providers', 'VoucherController@airtimeSwapProvider');
    
    //Data Route
    Route::post('data/buy', 'VoucherController@dataBuy');
    Route::post('data/providers', 'VoucherController@airtimeProvider');
    Route::post('data/plans', 'VoucherController@dataPlan');
    
    //Cable Route
    Route::post('cable/buy', 'VoucherController@cableBuy');
    Route::post('cable/card-verify', 'VoucherController@cableVerify');
    Route::post('cable/providers', 'VoucherController@cableProvider');
    Route::post('cable/plans', 'VoucherController@cablePlan');
    
    //Power Route
    Route::post('power/buy', 'VoucherController@powerBuy');
    Route::post('power/meter-verify', 'VoucherController@powerVerify');
    Route::post('power/providers', 'VoucherController@powerProvider');
    
    
    //Transfer Route
    Route::get('transfer/details/single', 'TransferController@otherSingleDetails');
    Route::get('transfer/details/bulk', 'TransferController@otherBulkDetails');
    Route::post('transfer/own', 'TransferController@own');
    Route::post('transfer/other', 'TransferController@other');
    Route::post('transfer/other/bulk', 'TransferController@bulkCsv');
    
    
    Route::fallback(function(){
        
      return response()->json(['responseMessage' => 'endpoint not exist', 'responseCode' => 404], 404);
        
    });
});
