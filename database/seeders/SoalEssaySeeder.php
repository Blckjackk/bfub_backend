<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SoalEssaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('soal_essay')->insert([
            [
                'cabang_lomba_id' => 1,
                'nomor_soal' => 1,
                'pertanyaan_essay' => 'Jelaskan konsep limit dalam matematika dan berikan contoh penerapannya!',
            ],
            [
                'cabang_lomba_id' => 1,
                'nomor_soal' => 2,
                'pertanyaan_essay' => 'Uraikan langkah-langkah menyelesaikan integral tak tentu!',
            ],
            [
                'cabang_lomba_id' => 1,
                'nomor_soal' => 3,
                'pertanyaan_essay' => 'Jelaskan hubungan antara turunan dan integral dalam kalkulus!',
            ],
            [
                'cabang_lomba_id' => 2,
                'nomor_soal' => 1,
                'pertanyaan_essay' => 'Jelaskan hukum Newton pertama dan berikan contoh penerapannya dalam kehidupan sehari-hari!',
            ],
            [
                'cabang_lomba_id' => 2,
                'nomor_soal' => 2,
                'pertanyaan_essay' => 'Uraikan konsep energi kinetik dan energi potensial beserta rumusnya!',
            ],
        ]);
    }
}
