<?php

use Illuminate\Database\Seeder;
use App\City;

class CitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $birmingham = new City;
        $birmingham->id = 1;
        $birmingham->name = "Birmingham";
        $birmingham->save();

        $london = new City;
        $london->id = 2;
        $london->name = "London";
        $london->save();

        $manchester = new City;
        $manchester->id = 3;
        $manchester->name = "Manchester";
        $manchester->save();

    }
}
