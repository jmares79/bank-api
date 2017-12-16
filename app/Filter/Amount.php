<?php

namespace App\Filter;

use App\Interfaces\FilterInterface;
use Illuminate\Database\Query\Builder;

/**
 * Amount filter
 */
class Amount implements FilterInterface
{
    public static function apply(Builder $builder, $value)
    {
        return $builder->where('amount', '>=', $value);
    }
}
