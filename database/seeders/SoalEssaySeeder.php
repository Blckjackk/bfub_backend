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
                'pertanyaan_essay' => 'Jelaskan konsep limit dalam matematika!',
            ],
            [
                'cabang_lomba_id' => 2,
                'pertanyaan_essay' => 'Jelaskan hukum Newton pertama!',
            ],
        ]);
    }
}
