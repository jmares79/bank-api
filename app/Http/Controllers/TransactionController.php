<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Service\TransactionService;
use App\Exceptions\TransactionNotFoundException;
use App\Exceptions\TransactionCreationException;
use App\Http\Requests\CreateTransactionPost;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    protected $transaction;

    public function __construct(TransactionService $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
    * Gets a transaction by customer and id
    *
    * @param integer $customerId
    * @param integer $transactionId
    *
    * @return mixed A transaction in JSON format
    * @throws Exception On error while getting a transaction
    */
    public function transaction(Request $request, $customerId, $transactionId)
    {
        try {
            $transaction = $this->transaction->getTransaction($customerId, $transactionId);

            return response()->json(['transaction' => $transaction], Response::HTTP_OK);
        } catch (TransactionNotFoundException $e) {
            return response()->json(['message' => "Transaction not found"], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json(['message' => "Error while getting transaction"], Response::HTTP_BAD_REQUEST);
        }
    }

   /**
    * Get a list of transactions by customer and an series of filters
    *
    * @param integer $customerId
    * @param float $amount
    * @param integer $year
    * @param integer $month
    * @param integer $day
    * @param integer $offset
    * @param integer $limit
    *
    * @return mixed A list of transactions in JSON format
    * @throws Exception On error while getting a transaction
    */
    public function transactions(Request $request, $customerId, $amount, $year, $month, $day,  $offset, $limit)
    {
        DB::connection()->enableQueryLog();
        $customer = DB::table('users')->where('users.id', $customerId);

        if ($customer->get()->isEmpty()) { return response()->json(['message' => 'No user'], Response::HTTP_NOT_FOUND); }

        try {
            $transactions = $this->transaction->getTransactions($customer, $amount,  $year, $month, $day,  $offset, $limit);

            return response()->json(['transactions' => $transactions], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['message' => "Error while getting transaction"], Response::HTTP_BAD_REQUEST);
        }
    }

    public function create(CreateTransactionPost $request)
    {
        $payload = json_decode($request->getContent());

        try {
            $this->transaction->create($payload);

            return response()->json(['message' => "Transaction created sucessfully"], Response::HTTP_CREATED);
        } catch (TransactionCreationException $e) {
            return response()->json(['message' => "Error while creating product"], Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            return response()->json(['message' => "Error while creating product"], Response::HTTP_BAD_REQUEST);
        }
    }
}
