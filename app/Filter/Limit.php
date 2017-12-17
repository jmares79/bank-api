<?php

namespace App\Filter;

use App\Interfaces\FilterInterface;
use Illuminate\Database\Query\Builder;

/**
 * Limit filter
 */
class Limit implements FilterInterface
{
    public static function apply(Builder $builder, $value)
    {
        return $builder->limit($value);
    }
}
