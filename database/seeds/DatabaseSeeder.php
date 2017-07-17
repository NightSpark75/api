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
        // $this->call(UsersTableSeeder::class);
        $data1 = array(
            'co' => 'C01',
            'prg_id' => 'MPEF0221',
            'web_route' => '/route',
            'status' => 'Y');
        $data2 = array(
            'co' => 'C01',
            'prg_id' => 'MPEF0220',
            'web_route' => '/route',
            'status' => 'Y');
        $data3 = array(
            'co' => 'C01',
            'prg_id' => 'SMAF0030',
            'web_route' => '/web/user',
            'status' => 'Y');
        DB::table('api_web_prg')->insert($data1);
        DB::table('api_web_prg')->insert($data2);
        DB::table('api_web_prg')->insert($data3);
    }
}
