<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JawabanEssaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('jawaban_essay')->insert([
            [
                'peserta_id' => 1,
                'soal_essay_id' => 1,
                'jawaban_teks' => 'Limit adalah nilai pendekatan suatu fungsi saat variabel mendekati nilai tertentu.',
            ],
            [
                'peserta_id' => 2,
                'soal_essay_id' => 2,
                'jawaban_teks' => 'Hukum Newton pertama menyatakan bahwa benda akan tetap diam atau bergerak lurus beraturan jika tidak ada gaya yang bekerja padanya.',
            ],
        ]);
    }
}
