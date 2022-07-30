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

/***************************** AuthController Start *****************************/
Route::post('sign-up-user'                 , 'AuthController@signUpUser');
Route::post('sign-up-provider'             , 'AuthController@signUpProvider');
Route::post('sign-up-delegate'             , 'AuthController@signUpDelegate');
Route::patch('activate'                    , 'AuthController@activate');
Route::get('resend-code'                   , 'AuthController@resendCode');
Route::post('sign-in'                      , 'AuthController@login');
Route::delete('sign-out'                   , 'AuthController@logout');


Route::group(['middleware' => ['auth:sanctum', 'is-active']], function () {

    Route::post('delegate-main-orders'           ,'DelegateController@delegate_main_orders');
    Route::post('delegate-pending-orders'        ,'DelegateController@delegate_pending_orders');
    Route::post('delegate-finished-orders'       ,'DelegateController@delegate_finished_orders');
    Route::post('delegate-order-details'         ,'DelegateController@delegate_order_details');
    Route::get('delegate-order-invoice/{id}'     ,'DelegateController@delegate_order_invoice');
    Route::get('delegate-financial-accounts'     ,'DelegateController@delegate_financial_accounts');
});

