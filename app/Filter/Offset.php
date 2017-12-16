<?php

namespace App\Filter;

use App\Interfaces\FilterInterface;
use Illuminate\Database\Query\Builder;

/**
 * Offset filter
 */
class Offset implements FilterInterface
{
    public static function apply(Builder $builder, $value)
    {
        return $builder->offset($value);
    }
}
