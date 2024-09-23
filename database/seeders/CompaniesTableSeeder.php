<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Company;

class CompaniesTableSeeder extends Seeder
{
    public function run()
    {
        $companies= [
            [
            'company_id' => 'c95sdr1e9seo9ptrpbbg',
            'name' => 'Europe',
            'email' => 'sheraz@recasoft.no',
            'phone' => '22222222',
            'service_account_id' => 'b7667ad7d7ca49aa8cc396f73785e334',
            'service_account_email' => 'ccktflnnsovg009a4i30@c95sdr1e9seo9ptrpbbg.serviceaccount.d21s.com',
            'key_id' => 'ccktg01df3t000bh6su0',
            'is_active' => 1,
            'parent_id' => 0,
            'organization_name' => 'Europe',
            'organization_no' => '12345678',
            ],
            [
                'company_id' => 'cdbe847o8esguul46s30',
                'name' => 'Test SH',
                'email' => 'sheraz@recasoft.no',
                'phone' => '22222222',
                'service_account_id' => 'c29c478e1c334ddcaf87bd8c205c883a',
                'service_account_email' => 'cdbe87257e60009ft2gg@cdbe847o8esguul46s30.serviceaccount.d21s.com',
                'key_id' => 'cdbe9ci57e60009ft2hg',
                'is_active' => 1,
                'parent_id' => 0,
                'organization_name' => 'Test SH',
                'organization_no' => '12345678',
            ]
        ];

        foreach ($companies as $company) {
            Company::create($company);
        }
        // You can add more company records as needed.
    }
}
