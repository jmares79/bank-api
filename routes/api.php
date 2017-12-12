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

$router->post('/login', 'LoginController@login');
$router->post('/login/refresh', 'LoginController@refresh');

Route::post('/customer', function () {
    //
})->middleware('auth:api');

Route::get('/transaction/{customerId}/{transactionId}', 'TransactionController@transaction')->middleware('auth:api')->where(['customerId' => '[0-9]+', 'transactionId' => '[0-9]+']);;

Route::get('/transactions/{id}/{amount}/{date}/{offset}/{limit}', function () {
    //
})->middleware('auth:api');

Route::post('/transaction', function () {
    //
})->middleware('auth:api');

Route::put('/transaction/{id}', function () {
    //
})->middleware('auth:api');

Route::delete('/transaction/{id}', function () {
    //
})->middleware('auth:api');
