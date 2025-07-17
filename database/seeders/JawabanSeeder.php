<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JawabanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('jawaban')->insert([
            [
                'peserta_id' => 1,
                'soal_id' => 1,
                'jawaban_peserta' => 'B',
                'benar' => true,
                'waktu_dijawab' => now(),
            ],
            [
                'peserta_id' => 2,
                'soal_id' => 2,
                'jawaban_peserta' => 'A',
                'benar' => true,
                'waktu_dijawab' => now(),
            ],
        ]);
    }
}
