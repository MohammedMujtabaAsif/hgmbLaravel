<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use App\User;
use Carbon\Carbon;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('App\User');

        for($i = 0; $i < 50; $i++){


            $firstName = $faker->firstName;
            $surname = $faker->lastName;

            $dob = $faker->date($format = 'Y-m-d', $max = '-18 years');
            $currentDateTime = Carbon::now();

            $user = User::create([
                'firstNames' => $firstName,
                'surname' => $surname,
                'prefName'  => $firstName,
                'email' => $faker->unique()->safeEmail,
                'email_verified_at' => now(),
                'password' => bcrypt('password'), // password
                'remember_token' => Str::random(10),
                'phoneNumber' => $faker->phoneNumber,
                'city_id' => $faker->numberBetween($min = 1, $max = 2),
                'gender_id' => $faker->numberBetween($min = 1, $max = 1),
                'marital_status_id' => $faker->numberBetween($min = 1, $max = 3),
                'dob' => $dob,
                'age' => Carbon::parse($dob)->diff(Carbon::now())->format('%y'),
                'numOfChildren' => $faker->numberBetween($min = 0, $max = 2),
                'bio'  => $faker->realText($maxNbChars = 200, $indexSize = 2),
                
                'prefMinAge' => $faker->numberBetween($min = 18, $max = 24),
                'prefMaxAge' => $faker->numberBetween($min = 25, $max = 35),
                'prefMaxNumOfChildren' => $faker->numberBetween($min = 0, $max = 3),



                'created_at' => $currentDateTime,
                'updated_at' => $currentDateTime,
            ]);

                    
            $user->prefCities()->sync($faker->numberBetween($min = 0, $max = 2));
            $user->prefGenders()->sync($faker->numberBetween($min = 0, $max = 1));
            $user->prefMaritalStatuses()->sync($faker->numberBetween($min = 0, $max = 2));

            $user->save();
        }
    }
}
