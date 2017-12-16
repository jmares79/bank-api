<?php

namespace App\Service;

use App\Exceptions\TransactionNotFoundException;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use App\Service\FilterService;
use App\Filter\Amount;
use App\Filter\Date;
use App\Filter\Offset;
use App\Filter\Limit;
use App\Transaction;

/**
 * @resource transaction
 *
 * Transaction service to handle all needed operations for them
 */
class TransactionService
{
    protected $transactions;
    protected $filterService;

    public function __construct(FilterService $filterService)
    {
        $this->filterService = $filterService;
    }

   /**
    * Gets a transaction by customer and id
    *
    * @param integer $customerId
    * @param integer $transactionId
    *
    * @return Transaction|null A transaction model or null if nothing found
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

   /**
    * Gets transactions by customer and an array of filters
    *
    * @param Builder $customer
    * @param integer $amount
    * @param integer $year
    * @param integer $month
    * @param integer $day
    * @param integer $offset
    * @param integer $limit
    *
    * @return mixed A list of transactions or an empty array
    */
    public function getTransactions(Builder $customer, $amount, $year, $month, $day, $offset, $limit)
    {
        $this->transactions = $customer->join('transactions', 'users.id', '=', 'transactions.user_id');
        $filters = $this->prepareQueryByFilters($amount, $year, $month, $day,  $offset, $limit);

        $transactions = $this->filterService->applyFilters($this->transactions, $filters);

        return $transactions;
    }

    /**
    * Prepare transactions filters for being applied
    *
    * @param integer $amount
    * @param integer $year
    * @param integer $month
    * @param integer $day
    * @param integer $offset
    * @param integer $limit
    *
    * @return mixed An array with the filter name as key and its value
    */
    protected function prepareQueryByFilters($amount, $year, $month, $day, $offset, $limit)
    {
        $date = $year.'-'.$month.'-'.$day;

        return compact('amount', 'date', 'offset', 'limit');
    }
}
