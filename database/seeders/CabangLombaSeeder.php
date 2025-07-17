<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CabangLombaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('cabang_lomba')->insert([
            [
                'nama_cabang' => 'Matematika',
                'deskripsi_lomba' => 'Lomba Matematika tingkat SMA',
                'waktu_mulai_pengerjaan' => now()->addDays(1),
                'waktu_akhir_pengerjaan' => now()->addDays(1)->addHours(2),
            ],
            [
                'nama_cabang' => 'Fisika',
                'deskripsi_lomba' => 'Lomba Fisika tingkat SMA',
                'waktu_mulai_pengerjaan' => now()->addDays(2),
                'waktu_akhir_pengerjaan' => now()->addDays(2)->addHours(2),
            ],
        ]);
    }
}
