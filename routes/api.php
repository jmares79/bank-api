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

$router->post('/login', 'Auth\LoginController@login');
$router->post('/login/refresh', 'Auth\LoginController@refresh');

Route::post('/customer', function () {
    //
})->middleware('auth:api');

Route::get('/transaction/{customerId}/{transactionId}', 'TransactionController@transaction')
    ->where(['customerId' => '[0-9]+', 'transactionId' => '[0-9]+'])->name('get-transaction');;

Route::get('/transactions/{customerId}/{amount}/{year}/{month}/{day}/{offset}/{limit}', 'TransactionController@transactions')
    ->where(['customerId' => '[0-9]+', 'year' => '[0-9]{4}', 'month' => '[2-9]|1[0-2]?', 'day' => '(0[1-9]|[12]\d|3[01])', 'offset' => '[0-9]+', 'limit' => '[0-9]+'])->name('get-filtered-transaction');

Route::post('/transaction', 'TransactionController@create')->name('create-transaction');
Route::put('/transaction', 'TransactionController@update')->name('update-transaction');
Route::delete('/transaction/{transactionId}', 'TransactionController@delete')->name('delete-transaction');

Route::get('/redirect', function () {
    $query = http_build_query([
        'client_id' => '1',
        'redirect_uri' => 'http://127.0.0.1:8000/callback',
        'response_type' => 'code',
        'scope' => '',
    ]);

    return redirect('http://127.0.0.1:8000/oauth/authorize?'.$query);
});

Route::get('/callback', function (\Illuminate\Http\Request $request) {
    $http = new GuzzleHttp\Client;

    $response = $http->post('http://127.0.0.1:8000/oauth/token', [
        'form_params' => [
            'grant_type' => 'authorization_code',
            'client_id' => '1',
            'client_secret' => 'C2XILc42XMoJTBKMyhTPAZmSTcwu3o4Ym8dLds4p',
            'redirect_uri' => 'http://127.0.0.1:8000/callback',
            'code' => $request->code,
        ],
    ]);

    return json_decode((string) $response->getBody(), true);
});
