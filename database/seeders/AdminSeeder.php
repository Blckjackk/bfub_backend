<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('admin')->insert([
            [
                'nama_admin' => 'Super Admin',
                'password_hash' => bcrypt('admin123'),
                'role' => 'admin',
            ],
            [
                'nama_admin' => 'Operator',
                'password_hash' => bcrypt('operator123'),
                'role' => 'admin',

            ],
        ]);
    }
}
