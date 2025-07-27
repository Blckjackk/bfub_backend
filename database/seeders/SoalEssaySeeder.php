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
            // Soal Essay untuk Cabang Lomba 1 (Matematika)
            [
                'cabang_lomba_id' => 1,
                'nomor_soal' => 1,
                'pertanyaan_essay' => 'Jelaskan konsep limit dalam matematika dan berikan contoh penerapannya dalam kehidupan sehari-hari!',
            ],
            [
                'cabang_lomba_id' => 1,
                'nomor_soal' => 2,
                'pertanyaan_essay' => 'Uraikan langkah-langkah menyelesaikan integral tak tentu dari fungsi polynomial! Berikan contoh konkret!',
            ],
            [
                'cabang_lomba_id' => 1,
                'nomor_soal' => 3,
                'pertanyaan_essay' => 'Jelaskan hubungan antara turunan dan integral dalam kalkulus! Mengapa keduanya disebut operasi kebalikan?',
            ],
            [
                'cabang_lomba_id' => 1,
                'nomor_soal' => 4,
                'pertanyaan_essay' => 'Buktikan bahwa jumlah sudut dalam segitiga adalah 180 derajat menggunakan geometri Euclidean!',
            ],
            [
                'cabang_lomba_id' => 1,
                'nomor_soal' => 5,
                'pertanyaan_essay' => 'Jelaskan konsep matriks dan operasi-operasi dasar yang dapat dilakukan pada matriks! Berikan contoh aplikasinya!',
            ],
            [
                'cabang_lomba_id' => 1,
                'nomor_soal' => 6,
                'pertanyaan_essay' => 'Uraikan metode pembuktian induksi matematika dan berikan contoh penggunaannya untuk membuktikan suatu formula!',
            ],
            [
                'cabang_lomba_id' => 1,
                'nomor_soal' => 7,
                'pertanyaan_essay' => 'Jelaskan konsep fungsi logaritma dan hubungannya dengan fungsi eksponensial! Sebutkan sifat-sifat logaritma!',
            ],
            [
                'cabang_lomba_id' => 1,
                'nomor_soal' => 8,
                'pertanyaan_essay' => 'Uraikan pengertian statistika deskriptif dan inferensial! Berikan contoh penggunaan masing-masing dalam penelitian!',
            ],
            [
                'cabang_lomba_id' => 1,
                'nomor_soal' => 9,
                'pertanyaan_essay' => 'Jelaskan konsep peluang dan cara menghitung peluang kejadian majemuk! Berikan contoh soal dan penyelesaiannya!',
            ],
            [
                'cabang_lomba_id' => 1,
                'nomor_soal' => 10,
                'pertanyaan_essay' => 'Uraikan teorema Pythagoras dan berbagai pembuktiannya! Jelaskan aplikasi teorema ini dalam kehidupan sehari-hari!',
            ],

            // Soal Essay untuk Cabang Lomba 2 (Fisika)
            [
                'cabang_lomba_id' => 2,
                'nomor_soal' => 1,
                'pertanyaan_essay' => 'Jelaskan hukum Newton pertama dan berikan contoh penerapannya dalam kehidupan sehari-hari!',
            ],
            [
                'cabang_lomba_id' => 2,
                'nomor_soal' => 2,
                'pertanyaan_essay' => 'Uraikan konsep energi kinetik dan energi potensial beserta rumusnya! Jelaskan hukum kekekalan energi!',
            ],
            [
                'cabang_lomba_id' => 2,
                'nomor_soal' => 3,
                'pertanyaan_essay' => 'Jelaskan fenomena gelombang elektromagnetik dan spektrumnya! Berikan contoh aplikasi dalam teknologi modern!',
            ],
            [
                'cabang_lomba_id' => 2,
                'nomor_soal' => 4,
                'pertanyaan_essay' => 'Uraikan konsep relativitas khusus Einstein dan dampaknya terhadap pemahaman ruang dan waktu!',
            ],
            [
                'cabang_lomba_id' => 2,
                'nomor_soal' => 5,
                'pertanyaan_essay' => 'Jelaskan prinsip kerja generator listrik dan transformator! Bagaimana keduanya berperan dalam sistem distribusi listrik?',
            ],
        ]);
    }
}
