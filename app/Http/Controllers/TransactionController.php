<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Service\TransactionService;

class TransactionController extends Controller
{
    protected $transaction;

    public function __construct(TransactionService $transaction)
    {
        $this->transaction = $transaction;
    }

    public function transaction($customerId, $transactionId)
    {
        try {
            $transaction = $this->transaction->getTrasaction($customerId, $transactionId);

            return response()->json(['transaction' => $transaction], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['message' => "Error while getting transaction"], Response::HTTP_BAD_REQUEST);
        }
    }
}
