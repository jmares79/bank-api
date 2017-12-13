<?php

namespace App\Service;

use App\Transaction;

/**
 * @resource transaction
 *
 * transaction service to handle all needed operations for them
 */
class TransactionService
{
    public function getTrasaction($customerId, $transactionId)
    {
        return \App\Transaction::where([
            ['user_id', '=', $customerId],
            ['id', '=', $transactionId]
        ])->get(['id', 'amount', 'date']);
    }
}
