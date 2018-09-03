<?php

use Faker\Generator as Faker;
use App\Eloquent\Models\Client;

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

$factory->define(Client::class, function (Faker $faker) {
    return [
        'id' => $faker->unique()->randomNumber,
        'name' => $faker->name,
        'address' => $faker->address,
    ];
});
