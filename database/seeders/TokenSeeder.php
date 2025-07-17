<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TokenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('token')->insert([
            [
                'kode_token' => 'MAT-TOKEN-001',
                'peserta_id' => 1,
                'cabang_lomba_id' => 1,
                'tipe' => 'utama',
                'status_token' => 'aktif',
                'created_at' => now(),
                'expired_at' => now()->addHours(2),
            ],
            [
                'kode_token' => 'FIS-TOKEN-001',
                'peserta_id' => 2,
                'cabang_lomba_id' => 2,
                'tipe' => 'utama',
                'status_token' => 'aktif',
                'created_at' => now(),
                'expired_at' => now()->addHours(2),
            ],
        ]);
    }
}
