<?php

use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admins')->insert([
        		[
        			'name'=>'Administrator',
        			'email'=>'admin@localhost.com',
        			'level'=>'super',
        			'password'=>bcrypt('12345678'),
        		],
        		[
        			'name'=>'Admin Current',
        			'email'=>'current@localhost.com',
        			'level'=>'admin',
        			'password'=>bcrypt('12345678'),
        		],
        	]);
    }
}
