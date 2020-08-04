<?php

use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(MaritalStatusesTableSeeder::class);
        $this->call(GendersTableSeeder::class);
        $this->call(CitiesTableSeeder::class);
        $this->call([UserTableSeeder::class]);

    }
}
