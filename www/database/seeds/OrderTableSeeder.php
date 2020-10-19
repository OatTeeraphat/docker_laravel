<?php

use App\Model\Bill;
use App\Model\Cause;
use App\Model\Order;
use App\Model\Part;
use App\Model\Payment;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class OrderTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('cause')->truncate();
        DB::table('bill')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Cause::create([ 'desc' => 'ลูกค้าเป็นผู้ยกเลิก']);
        Cause::create([ 'desc' => 'งานตามใบรับงานนี้ไม่ถูกต้อง']);
        Cause::create([ 'desc' => 'ไม่รักไม่ต้องมาแคร์ไม่ต้องมาดีกับฉัน']);
        Cause::create([ 'desc' => 'ไม่รักก็ไม่ต้องมาห่วงฉัน']);

        $bill = Bill::create([
            'date' => '05/03/2562',
            'date_'=> Carbon::parse('2019-03-05'),
            'bill_id' => '101-610611-001',
            'status' => 0,
            'activate' => 1,
            'process' => 1,
            'deliver' => 0,
            'pay' => 0,
            'user_id' => 1,
            'customer_id' => 3,
            'branch_id' => 1,
            'image_part'=> null,
            'job_type' => '1',
            'cash' => 200,
        ]);

        Order::create([
            'bill_ref' => $bill->id,
            'date' => '05/03/2562',
            'date_'=> Carbon::parse('2019-03-05'),
            'customer_id' => 3,
            'user_id' => 1,
            'job_id' => 1,
            'amulet_id'=> 3,
            'amount' => 2,
            'branch_id' => 1,
            'price' => 500.00,
            'activate' => 1,
        ]);

        Order::create([
            'bill_ref' => $bill->id,
            'date' => '05/03/2562',
            'date_'=> Carbon::parse('2019-03-05'),
            'customer_id' => 3,
            'user_id' => 1,
            'job_id' => 2,
            'amulet_id'=> 3,
            'amount' => 2,
            'branch_id' => 1,
            'price' => 100.00,
            'activate' => 1,
        ]);

        Part::Create([
            'bill_ref' => $bill->id,
            'material_id' => 2,
            'price' => 100.00,
        ]);

        Payment::Create([
            'bill_ref' => $bill->id,
            'method' => 'cash',
            'value' => 200,
            'activate' => 1,
            'user_recive' => 1,
            'branch_id' => 1,
        ]);


        $bill2 = Bill::create([
            'date' => '04/03/2562',
            'date_'=> Carbon::parse('2019-03-04'),
            'bill_id' => '101-610611-001',
            'status' => 0,
            'activate' => 1,
            'process' => 0,
            'deliver' => 0,
            'pay' => 0,
            'user_id' => 1,
            'customer_id' => 3,
            'branch_id' => 1,
            'image_part'=> null,
            'job_type' => '1',
            'cash' => 200,
        ]);

        Order::create([
            'bill_ref' => $bill2->id,
            'date' => '05/03/2562',
            'date_'=> Carbon::parse('2019-03-05'),
            'customer_id' => 3,
            'user_id' => 1,
            'job_id' => 1,
            'amulet_id'=> 3,
            'amount' => 2,
            'branch_id' => 1,
            'price' => 500.00,
            'activate' => 1,
        ]);




    }
}
