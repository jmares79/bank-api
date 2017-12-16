<?php

namespace App\Service;

use App\Exceptions\TransactionNotFoundException;
use Illuminate\Database\Query\Builder;
use App\Transaction;

/**
 * @resource transaction
 *
 * Transaction service to handle all needed operations for them
 */
class FilterService
{
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
}
