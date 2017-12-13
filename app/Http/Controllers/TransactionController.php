<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Service\TransactionService;
use App\Exceptions\TransactionNotFoundException;

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
    public function transaction($customerId, $transactionId)
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
}
