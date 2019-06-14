<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Illuminate\Support\Str;
use Faker\Generator as Faker;

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

$factory->define(\App\Seat::class, function (Faker $faker) {
    return [
//        'reservation_id' => 1,
        'row_number' => $faker->randomNumber(1),
        'seat_number' => $faker->randomNumber(1),
    ];
});
$factory->define(\App\Reservation::class, function (Faker $faker) {
    return [
        'user_id' => $faker->randomNumber(),
        'projection_id' => $faker->randomNumber(),
        'price' => $faker->randomFloat(2, 1, 10),
        'currency' => 'USD',
        'status' => \App\Reservation::PENDING_STATUS
    ];
});
$factory->define(\App\PaypalPayment::class, function (Faker $faker) {
    return [
//        'reservation_id' => 1,
        'payment_id' => $faker->word,
//        'payer_id' => 1,
        'payment_state' => 'created',
        'price' => $faker->randomFloat(2, 1, 10),
        'currency' => 'USD',
        'approval_link' => $faker->url
    ];
});

