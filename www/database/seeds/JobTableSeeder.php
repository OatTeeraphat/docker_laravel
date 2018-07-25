<?php

use App\Model\Amulet;
use App\Model\Job;
use App\Model\Material;

use App\Model\Setting;
use Illuminate\Database\Seeder;

class JobTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('job')->truncate();
        DB::table('material')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $job1 = Job::create(['name' => 'ตัด(ลด)', 'order' => '0']);
        $job2 = Job::create(['name' => 'ต่อ(ขยาย)', 'order' => '1']);
        $job3 = Job::create(['name' => 'ผัง', 'order' => '2']);
        $job4 = Job::create(['name' => 'ชุบ', 'order' => '3']);
        $job5 = Job::create(['name' => 'อื่นๆ', 'order' => '4']);

        $material1  = Material::create(['name' => 'เพชร','activate' => 1, 'order' => '1']);
        $material2  = Material::create(['name' => 'พลอย','activate' => 1, 'order' => '2']);
        $material3  = Material::create(['name' => 'เงิน','activate' => 1, 'order' => '3']);
        $material4  = Material::create(['name' => 'อื่นๆ','activate' => 1, 'order' => '4']);

        $amulet1 = Amulet::create(['name' => 'แหวน', 'order' => '0']);
        $amulet2 = Amulet::create(['name' => 'ต่างหู', 'order' => '1']);
        $amulet3 = Amulet::create(['name' => 'สร้อยคอ', 'order' => '2']);
        $amulet4 = Amulet::create(['name' => 'สร้อยมือ', 'order' => '3']);
        $amulet5 = Amulet::create(['name' => 'กำไล', 'order' => '4']);
        $amulet6 = Amulet::create(['name' => 'จี้', 'order' => '5']);
        $amulet7 = Amulet::create(['name' => 'อื่นๆ', 'order' => '6']);

        $setting = Setting::create([
            'head_r_1' => 'บริการ ชุบซ่อมแซมเครื่องประดับอัญมณีทุกชนิดด้วยแสงเลเซอร์',
            'head_r_2' => 'http://jewelryclinic.co.th',
            'btm_l_1' => 'ชิ้นงานที่นำมาซ่อม หากเกิดความเสียหายโดยมิได้เจตนา ทางร้านขอสงวนสิทธิ์ความรับผิดชอบไม่เกิน 2,000 บาท',
            'btm_l_2' => 'The Stroe reserves the right to be liable for possible damage at more than 2,000 Bath',
            'btm_r_1' => 'ได้รับคืนสินค้าในสภาพเรียบร้อยดี',
            'btm_r_2' => 'Get the job in good condition.'
        ]);

    }
}
