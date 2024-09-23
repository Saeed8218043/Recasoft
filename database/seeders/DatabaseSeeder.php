<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CompaniesTableSeeder::class);
        $this->call(CompanyMemberInviteSeeder::class);
        $this->call(CompanyMemberSeeder::class);
        $this->call(DevicesTableSeeder::class);
        $this->call(DeviceTemperatureSeeder::class);
        $this->call(UsersTableSeeder::class);
    }
}
