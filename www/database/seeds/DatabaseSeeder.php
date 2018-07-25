<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(CustomerTableSeeder::class);
        $this->call(JobTableSeeder::class);
        $this->call(OrderTableSeeder::class);
    }
}
