<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Transaction;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Transaction::class, function (Faker $faker) {
    return [
        'from' => $faker->numberBetween(1,10),
        'to' => $faker->numberBetween(1,10),
        'amount' => $faker->numberBetween(1,1000),
        'details' => $faker->words(5,true),
    ];
});
