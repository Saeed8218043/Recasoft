<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class CompanyMemberInviteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('company_members_invite')->insert([
            "id" => "1",
            "email" => "sheraz@recasoft.no",
            "company_id" => "c95sdr1e9seo9ptrpbbg",
            "accepted" => "1",
        ],);
    }
}
