<?php


use App\Model\Craft;
use Illuminate\Database\Seeder;
use Ultraware\Roles\Models\Role;
use App\User;
use App\Branch;
use App\BranchIP;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('roles')->truncate();
        DB::table('users')->truncate();
        DB::table('role_user')->truncate();
        DB::table('branch')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $branchName = Branch::create([
            'name' => 'งามดูพลี',
            'address' => 'ซอยงามดูพลี พระราม4 100 เมตร จากปากซอย',
            'time_open' => '09:00น. - 19:00น.',
            'date_open' => 'จันทร์ - เสาร์',
            'increment'=> 0,
            'phone' => '0-2286-7777',
            'activate' => 1
        ]);

        $branchName2 = Branch::create([
            'name' => 'The Mall งามวงศ์วาน',
            'address' => 'ชั้น 2 ทางออกลานจอดรถ',
            'time_open' => '11:00น. - 19:00น.',
            'date_open' => 'ทุกวัน',
            'increment'=> 0,
            'phone' => '0-2286-7777',
            'activate' => 1
        ]);

        $branchName3 = Branch::create([
            'name' => 'สยามพารากอน',
            'address' => 'ชั้น B โซน North lift',
            'time_open' => '11:00น. - 19:00น.',
            'date_open' => 'ทุกวัน',
            'increment'=> 0,
            'phone' => '0-2286-7777',
            'activate' => 1
        ]);

        $branchIP = BranchIP::create([
            'ip' => '127.0.0.0',
            'branch_id' => $branchName->id
        ]);

        $adminRole = Role::create([
            'name' => 'admin',
            'slug' => 'admin',
            'description' => 'ผู้ควบคุมระบบ', // optional
            'level' => 4, // optional, set to 1 by default
        ]);

        $adminUser = User::create([
            'name' => 'admin',
            'u_name' => 'แอดมิน',
            'email' => 'admin@admin.com',
            'password' => bcrypt('111111'),
            'branch_id' => $branchName->id,
            'status' => 0,
            'activate' => 1,
            'pin' => 1111
        ]);

        $managerRole = Role::create([
            'name' => 'manager',
            'slug' => 'manager',
            'description' => 'ผู้จัดการสาขา', // optional
            'level' => 3, // optional, set to 1 by default
        ]);

        $managerUser = User::create([
            'name' => 'manager',
            'u_name' => 'ผู้จัดการสาขา',
            'email' => 'manager@manager.com',
            'password' => bcrypt('111111'),
            'branch_id' => $branchName->id,
            'status' => 0,
            'activate' => 1,
            'pin' => 1111
        ]);

        $staffRole = Role::create([
            'name' => 'staff',
            'slug' => 'staff',
            'description' => 'ธุรการสาขา', // optional
            'level' => 2, // optional, set to 1 by default
        ]);

        $staffUser = User::create([
            'name' => 'staff',
            'u_name' => 'พนักงานสาขา',
            'email' => 'staff@staff.com',
            'password' => bcrypt('111111'),
            'branch_id' => $branchName->id,
            'status' => 0,
            'activate' => 1,
            'pin' => 1111
        ]);

        
        $user = User::find(1);
        $user->attachRole($adminRole);
        
        $user = User::find(2);
        $user->attachRole($managerRole);

        $user = User::find(3);
        $user->attachRole($staffRole);

        Craft::create([
            'name'=> 'สมชาย',
            'branch_id'=> $branchName->id,
            'activate'=> 1
        ]);

        Craft::create([
            'name'=> 'สมศักดิ์',
            'branch_id'=> $branchName->id,
            'activate'=> 1
        ]);

        //$this->command->info('Seeding User Complete!!');

    }


}
