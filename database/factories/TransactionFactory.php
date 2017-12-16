<?php

use Faker\Generator as Faker;

$factory->define(App\Transaction::class, function (Faker $faker) {
    return [
        'amount' => $faker->randomFloat(0, 155, 155)
    ];
});
