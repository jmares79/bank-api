<?php

namespace App\Service;

use App\Exceptions\TransactionNotFoundException;
use App\Exceptions\TransactionCreationException;
use App\Exceptions\TransactionUpdateException;
use App\Exceptions\TransactionDeleteException;
use \Illuminate\Database\QueryException;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use App\Service\FilterService;
use Illuminate\Http\Request;
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
    * @throws TransactionNotFoundException On retrieve error
    */
    public function getTransaction($customerId, $transactionId)
    {
        $transaction = \App\Transaction::where([
            ['user_id', '=', $customerId],
            ['id', '=', $transactionId]
        ])->get(['id', 'amount', 'created_at AS date'])->first();

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
    * Creates a new transaction
    *
    * @param mixed $payload Array with transaction request data
    *
    * @return mixed An array with the information of the created transaction
    * @throws TransactionCreationException On creation error
    */
    public function create($payload)
    {
        try {
            $transaction = new Transaction;

            $transaction->amount = $payload->amount;

            $user = \App\User::find($payload->customerId);
            $user->transactions()->save($transaction);

            return $transaction::select('id AS transactionId', 'user_id AS customerId','amount', 'created_at AS date')
                                ->where('id', $transaction->id)->first();
        } catch (QueryException $e) {
            throw new TransactionCreationException();
        }
    }

   /**
    * Updates an existing transaction
    *
    * @param Builder $transaction A Builder object with the fetched transaction
    * @param numeric $newAmount The data for updating the transaction
    *
    * @return mixed An array with the information of the updated transaction
    * @throws TransactionUpdateException On creation error
    */
    public function update(Builder $transaction, $newAmount)
    {
        try {
            $transaction->update(['amount' => $newAmount]);

            $transaction->join('users', 'users.id', '=', 'transactions.user_id')
                        ->select(['transactions.id as transactionId', 'users.id AS customerId', 'amount', 'transactions.created_at AS date'])
                        ->get();

            return $transaction->get()->first();
        } catch (QueryException $e) {
            throw new TransactionUpdateException();
        }
    }

   /**
    * Deletes an existing transaction
    *
    * @param Transaction $transaction The transaction to be deleted
    *
    * @return boolean Wheter the deletion was successful or not
    * @throws TransactionDeleteException On deletion error
    */
    public function delete(Transaction $transaction)
    {
        try {
            $transaction->delete();

        } catch (QueryException $e) {
            throw new TransactionDeleteException();
        }
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

   /**
    * Checks whether a transaction payload is valid or not
    *
    * @param Request $request The transaction request
    *
    * @return boolean Wheter the payload is valid or not
    */
    public function isValid(Request $request)
    {
        return ($request->route('amount') > 0 && $request->route('offset') >= 1 && $request->route('limit') > 0);
    }
}
