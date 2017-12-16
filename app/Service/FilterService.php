<?php

namespace App\Service;

use App\Exceptions\TransactionNotFoundException;
use Illuminate\Database\Query\Builder;
use App\Transaction;

/**
 * @resource Filter
 *
 * Service to handle all filters operations
 */
class FilterService
{
   /**
    * Creates a full PSR-4 path to an applied filter
    *
    * @param string $name The name of the filter
    *
    * @return string A PSR-4 path to the corresponding filter class
    */
    public function getFilter($name)
    {
        $filter = 'App\\Filter\\' .
                str_replace(' ', '',
                    ucwords(
                        str_replace('_', ' ', $name)
                    )
                );

        return $filter;
    }

    /**
    * Applies all previously created filters
    *
    * @param Builder $transactions Transaction builder object to apply filters
    * @param mixed $filters Array of filters to be applied
    *
    * @return string A PSR-4 path to the corresponding filter class
    */
    public function applyFilters(Builder $transactions, $filters)
    {
        foreach ($filters as $name => $value) {
            $filter = $this->getFilter($name);

            if (class_exists($filter)) {
                $transactions = $filter::apply($transactions, $value);
            }
        }

        return $transactions->get(['transactions.id', 'amount', 'transactions.created_at AS date']);
    }
}
