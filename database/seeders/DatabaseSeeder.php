<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'admin',
            'email' => 'admin@argon.com',
            'password' => bcrypt('secret'),
            'phone'=> '0912341234',
            'point' => 0,
            'numberSpin' => 1
        ]);
    }
}
