<?php

use Illuminate\Database\Seeder;
use App\Gender;

class GendersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $female = new Gender;
        $female->id = 1;
        $female->name = "Female";
        $female->save();

        $male = new Gender;
        $male->id = 2;
        $male->name = "Male";
        $male->save();
    }
}
