<?php

use App\Model\Customer;
use Illuminate\Database\Seeder;

class CustomerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('customer')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $customer1 = Customer::create([
            'name' => 'สด',
            'phone' => '-',
            'customer_type' => 'สด1',
            'address' => '-',
            'line' => '-',
            'activate' => 1,
            'increment' => 0,
            'already_used' => 1
        ]);

        $customer2 = Customer::create([
            'name' => 'ชาติชาย ชายแท้',
            'phone' => '086-000-0000',
            'customer_type' => 'สด1',
            'address' => '',
            'line' => 'chatchai',
            'activate' => 1,
            'increment' => 0,
            'already_used' => 1
        ]);

        $customer3 = Customer::create([
            'name' => 'บริษัท จิวเวอรี่คลับ จำกัด',
            'phone' => '089-000-0000',
            'customer_type' => 'สด2',
            'address' => '37/2 Suthisarnvinijchai Rd., Samseannok, Huaykwang 10320 Bangkok, Thailand',
            'line' => 'jewclub',
            'activate' => 1,
            'increment' => 0,
            'already_used' => 1
        ]);

        //$this->command->info('Seeding Customer Complete!!');


    }
}
