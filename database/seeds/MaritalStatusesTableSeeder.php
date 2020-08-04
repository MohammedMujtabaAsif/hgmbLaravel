<?php

use Illuminate\Database\Seeder;
use App\MaritalStatus;

class MaritalStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $single = new MaritalStatus;
        $single->id = 1;
        $single->name = "Single";
        $single->save();

        $divorced = new MaritalStatus;
        $divorced->id = 2;
        $divorced->name = "Divorced";
        $divorced->save();

        $widowed = new MaritalStatus;
        $widowed->id = 3;
        $widowed->name = "Widowed";
        $widowed->save();
    }
}
