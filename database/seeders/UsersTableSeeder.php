<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            'id' => 1,
            'name' => 'Admin',
            'email' => 'admin@recasoft.com',
            'password' => bcrypt('123456'),
        ]);

        DB::table('users')->insert([
            'id' => 2,
            'name' => 'sheraz',
            'email' => 'sheraz@recasoft.no',
            'password' => bcrypt('123456'),
        ]);

        // You can add more user records as needed.
    }
}
