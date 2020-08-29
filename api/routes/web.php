<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('api')->group(function() {
    Route::get('accounts/{id}', 'AccountController@account');
    Route::get('accounts/{id}/transactions', 'AccountController@transactions');
    Route::post('accounts/{id}/transactions', 'AccountController@makeTransaction');
    Route::post('accounts/{id}/logout', 'AccountController@logout');
    Route::get('currencies', 'AccountController@currencies');
});
