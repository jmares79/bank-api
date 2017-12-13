<?php

namespace App\Service;

use App\Exceptions\TransactionNotFoundException;
use App\Transaction;

/**
 * @resource transaction
 *
 * transaction service to handle all needed operations for them
 */
class TransactionService
{
   /**
    * Gets a transaction by customer and id
    *
    * @param integer $customerId
    * @param integer $transactionId
    *
    * @return Transaction|null A transacion model or null if nothing found
    */
    public function getTransaction($customerId, $transactionId)
    {
        $transaction = \App\Transaction::where([
            ['user_id', '=', $customerId],
            ['id', '=', $transactionId]
        ])->get(['id', 'amount', 'date'])->first();

        if (!$transaction) { throw new TransactionNotFoundException(); }

        return $transaction;
    }
}
