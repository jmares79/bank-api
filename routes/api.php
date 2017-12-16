<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// $router->post('/login', 'LoginController@login');
// $router->post('/login/refresh', 'LoginController@refresh');

Route::post('/customer', function () {
    //
})->middleware('auth:api');

Route::get('/transaction/{customerId}/{transactionId}', 'TransactionController@transaction')
    ->where(['customerId' => '[0-9]+', 'transactionId' => '[0-9]+'])->name('get-transaction');;

Route::get('/transactions/{customerId}/{amount}/{year}/{month}/{day}/{offset}/{limit}', 'TransactionController@transactions')
    ->where(['customerId' => '[0-9]+', 'offset' => '[0-9]+', 'limit' => '[0-9]+'])->name('get-filtered-transaction');

Route::post('/transaction', function () {
    //
})->middleware('auth:api');

Route::put('/transaction/{id}', function () {
    //
})->middleware('auth:api');

Route::delete('/transaction/{id}', function () {
    //
})->middleware('auth:api');
