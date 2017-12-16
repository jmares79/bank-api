<?php

namespace App\Service;

use App\Exceptions\TransactionNotFoundException;
use Illuminate\Database\Query\Builder;
use App\Service\FilterService;
use App\Filter\Amount;
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

        $transactions = $this->getFilteredTransactions($filters);

        return $transactions;
    }

    protected function prepareQueryByFilters($amount, $year, $month, $day, $offset, $limit)
    {
        return compact('amount', 'year', 'month', 'day', 'offset', 'limit');
    }

    protected function getFilteredTransactions($filters)
    {
        foreach ($filters as $name => $value) {
            var_dump($name, $value);
            $filter = $this->filterService->getFilter($name);
            // dd(class_exists($filter));
            if (class_exists($filter)) {
                $this->transactions = $filter::apply($this->transactions, $value);
            }

            dd($this->transactions->get());
        }
    }


}
