<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Service\TransactionService;
use App\Exceptions\TransactionNotFoundException;
use App\Exceptions\TransactionCreationException;
use App\Exceptions\TransactionUpdateException;
use App\Exceptions\TransactionDeleteException;
use App\Http\Requests\CreateTransactionPost;
use App\Http\Requests\UpdateTransactionPut;
use Illuminate\Support\Facades\DB;
use App\Transaction;
use App\User;

class TransactionController extends Controller
{
    protected $transaction;

    public function __construct(TransactionService $transaction)
    {
        $this->transaction = $transaction;
    }

   /**
    * Gets all transactions for a customer
    *
    * @param integer $customerId
    *
    * @return mixed An array with all transactions in JSON format
    * @return Response HTTP 200 on success,
    * @return Response HTTP 400 on retrieval error,
    *
    * @throws Exception On error while getting the transactions
    */
    public function getAllTransactions(Request $request, $customerId)
    {
        $customer = \App\User::find($customerId);
        if (null == $customer) { return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND); }

        try {
            $transactions = $customer->transactions()->get();

            return response()->json(['transaction' => $transactions], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['message' => "Error while getting transaction"], Response::HTTP_BAD_REQUEST);
        }
    }

   /**
    * Gets a transaction by customer and transaction id
    *
    * @param integer $customerId
    * @param integer $transactionId
    *
    * @return mixed A transaction in JSON format
    * @return Response HTTP 200 on success,
    * @return Response HTTP 404 when non existing transaction,
    * @return Response HTTP 400 on retrieval error,
    *
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
    * @return Response HTTP 200 on success,
    * @return Response HTTP 422 when invalid request,
    * @return Response HTTP 404 when non existing transaction,
    * @return Response HTTP 400 on creation error,
    *
    * @throws Exception On error while getting a transaction
    */
    public function transactions(Request $request, $customerId, $amount, $year, $month, $day,  $offset, $limit)
    {
        DB::connection()->enableQueryLog();
        $customer = DB::table('users')->where('users.id', $customerId);

        if ($customer->get()->isEmpty()) { return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND); }
        if (!$this->transaction->isValid($request)) { return response()->json(['message' => 'Invalid parameters'], Response::HTTP_UNPROCESSABLE_ENTITY); }

        try {
            $transactions = $this->transaction->getTransactions($customer, $amount,  $year, $month, $day,  $offset, $limit);

            return response()->json(['transactions' => $transactions], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['message' => "Error while getting transaction"], Response::HTTP_BAD_REQUEST);
        }
    }

   /**
    * Creates a new transaction based on information in the request
    *
    * @param CreateTransactionPost $request The validated information
    *
    * @return Response HTTP 201 on success,
    * @return Response HTTP 422 when invalid parameters,
    * @return Response HTTP 400 on creation error,
    *
    * @throws TransactionCreationException|Exception On error while creating a transaction
    */
    public function create(CreateTransactionPost $request)
    {
        DB::connection()->enableQueryLog();
        $payload = json_decode($request->getContent());
        $customer = DB::table('users')->where('users.id', $payload->customerId);

        if ($customer->get()->isEmpty()) { return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND); }

        try {
            $response = $this->transaction->create($payload);

            return response()->json(['transaction' => $response], Response::HTTP_CREATED);
        } catch (TransactionCreationException $e) {
            return response()->json(['transaction' => "Error while creating transaction"], Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            return response()->json(['transaction' => "Error while creating transaction"], Response::HTTP_BAD_REQUEST);
        }
    }

   /**
    * Updates a transaction based on information in the request
    *
    * @param UpdateTransactionPut $request The validated information
    *
    * @return Response HTTP 200 on success,
    * @return Response HTTP 422 when invalid parameters,
    * @return Response HTTP 404 when non existing transaction,
    * @return Response HTTP 400 on creation error,
    *
    * @throws TransactionUpdateException|Exception On error while updating a transaction
    */
    public function update(UpdateTransactionPut $request)
    {
        $payload = json_decode($request->getContent());
        $transaction = DB::table('transactions')->where('transactions.id', $payload->transactionId);

        if ($transaction->get()->isEmpty()) { return response()->json(['message' => 'Transaction not found'], Response::HTTP_NOT_FOUND); }

        try {
            $response = $this->transaction->update($transaction, $payload->amount);

            return response()->json(['transaction' => $response], Response::HTTP_OK);
        } catch (TransactionUpdateException $e) {
            return response()->json(['transaction' => "Error while updating transaction"], Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            return response()->json(['transaction' => "Error while updating transaction"], Response::HTTP_BAD_REQUEST);
        }
    }

   /**
    * Deletes a transaction by its Id
    *
    * @param integer $transactionId
    *
    * @return Response HTTP 200 on success,
    * @return Response HTTP 422 when invalid parameters,
    * @return Response HTTP 404 when non existing transaction,
    * @return Response HTTP 400 on creation error,
    *
    * @throws TransactionUpdateException|Exception On error while updating a transaction
    */
    public function delete($transactionId)
    {
        $transaction = Transaction::find($transactionId);

        if (null == $transaction) { return response()->json(['message' => 'Transaction not found'], Response::HTTP_NOT_FOUND); }

        try {
            $response = $this->transaction->delete($transaction);

            return response()->json(['transaction' => "Transaction deleted sucessfully"], Response::HTTP_OK);
        } catch (TransactionDeleteException $e) {
            return response()->json(['transaction' => "Error while deleting transaction"], Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            return response()->json(['transaction' => "Error while deleting transaction"], Response::HTTP_BAD_REQUEST);
        }
    }
}
