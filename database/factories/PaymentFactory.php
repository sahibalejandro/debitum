<?php

use Faker\Generator as Faker;

$factory->define(App\Payment::class, function (Faker $faker) {
    $repeatPeriod = $repeatDesignator = null;

    if ($faker->boolean) {
        $repeatPeriod = $faker->numberBetween(1, 2);
        $repeatDesignator = $faker->randomElement(['days', 'weeks', 'months', 'years']);
    }

    $paidAt = null;
    if ($faker->boolean) {
        $paidAt = $faker->dateTimeBetween('-2 weeks', '-1 week');
    }

    return [
        'user_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'name' => $faker->text(30),
        'amount' => $faker->numberBetween(10000, 1000000),
        'due_date' => $faker->dateTimeBetween('-1 weeks', '1 weeks')->format('Y-m-d'),
        'repeat_period' => $repeatPeriod,
        'repeat_designator' => $repeatDesignator,
        'paid_at' => $paidAt,
    ];
});
