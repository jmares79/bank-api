<?php

namespace App\Interfaces;

use Illuminate\Database\Query\Builder;

/**
 *  Interface for a parameter filter
 */
interface FilterInterface
{
    /**
     * Apply a given filter value to the builder instance.
     *
     * @param Builder $builder
     * @param mixed $value
     * @return Builder $builder
     */
    public static function apply(Builder $builder, $value);
}
