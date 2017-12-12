<?php

use Faker\Generator as Faker;

$factory->define(App\Transaction::class, function (Faker $faker) {
    return [
        'amount' => $faker->randomFloat(2, 1, 3500),
        'date' => $faker->dateTimeThisYear
    ];
});
