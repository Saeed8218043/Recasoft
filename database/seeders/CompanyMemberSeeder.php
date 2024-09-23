<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class CompanyMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('company_members')->insert( [
            "id" => "1",
            "user_id" => "2",
            "comp_id" => "1",
            "email" => "sheraz@recasoft.no",
            "company_name" => "Europe",
            "role" => "0",
            "company_id" => "c95sdr1e9seo9ptrpbbg",
            "parent_id" => "0",
            "created_at" => "2022-09-20 17:36:55",
            "updated_at" => null,
        ]);
    }
}
