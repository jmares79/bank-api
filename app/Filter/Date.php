<?php

namespace App\Filter;

use App\Interfaces\FilterInterface;
use Illuminate\Database\Query\Builder;

/**
 * Date filter
 */
class Date implements FilterInterface
{
    public static function apply(Builder $builder, $value)
    {
        return $builder->whereDate('transactions.created_at', '=', $value);
    }
}
