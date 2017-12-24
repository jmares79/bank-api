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

$router->post('/login', 'Auth\LoginController@login')->middleware('cors');
Route::options('/login', function() { return; })->middleware('cors');

$router->post('/login/refresh', 'Auth\LoginController@refresh')->middleware('cors');
Route::options('/login/refresh', function() { return; })->middleware('cors');


Route::middleware('auth:api')
    ->get('/transactions/{customerId}', 'TransactionController@getAllTransactions')
    ->name('get-all-transactions')
    ->middleware('cors');
Route::options('/transactions/{customerId}', function() { return; })->middleware('cors');

Route::get('/transaction/{customerId}/{transactionId}', 'TransactionController@transaction')
    ->where(['customerId' => '[0-9]+', 'transactionId' => '[0-9]+'])->name('get-transaction')->middleware('cors');

Route::get('/transactions/{customerId}/{amount}/{year}/{month}/{day}/{offset}/{limit}', 'TransactionController@transactions')
    ->where(['customerId' => '[0-9]+', 'year' => '[0-9]{4}', 'month' => '[2-9]|1[0-2]?', 'day' => '(0[1-9]|[12]\d|3[01])', 'offset' => '[0-9]+', 'limit' => '[0-9]+'])->name('get-filtered-transaction');

Route::post('/transaction', 'TransactionController@create')->name('create-transaction');
Route::put('/transaction', 'TransactionController@update')->name('update-transaction');
Route::delete('/transaction/{transactionId}', 'TransactionController@delete')->name('delete-transaction');
