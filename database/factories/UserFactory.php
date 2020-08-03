<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Carbon\Carbon;

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

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->firstName,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token' => Str::random(10),

        // 'firstNames' => $faker->firstName,
        // 'surname' => $faker->lastName,
        // 'prefName'  => $faker->name,
        // 'email' => $faker->unique()->safeEmail,
        // 'email_verified_at' => now(),'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        // 'remember_token' => Str::random(10),
        // 'phoneNumber' => $faker->phoneNumber,
        // 'userCity' => $faker->numberBetween($min = 0, $max = 2),
        // 'gender' => $faker->numberBetween($min = 0, $max = 1),
        // 'maritalStatus' => $faker->numberBetween($min = 0, $max = 2),
        // 'dob' => $faker->date($format = 'Y-m-d', $max = '-18 years'),
        // 'age' => Carbon::parse($data['dob'])->diff(Carbon::now())->format('%y'),
        // 'numOfChildren' => $faker->numberBetween($min = 0, $max = 2),
        // 'bio'  => $faker->realText($maxNbChars = 200, $indexSize = 2),
    ];
});
