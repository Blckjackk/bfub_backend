<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SoalIsianSingkat;

class SoalIsianSingkatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $soalIsianSingkat = [
            // Soal Isian Singkat untuk cabang lomba 1 (Matematika)
            [
                'cabang_lomba_id' => 1,
                'pertanyaan_isian' => 'Berapa hasil dari 15 + 27?',
                'jawaban_benar' => '42',
                'nomor_soal' => 1
            ],
            [
                'cabang_lomba_id' => 1,
                'pertanyaan_isian' => 'Sebutkan bilangan prima terkecil yang lebih besar dari 10!',
                'jawaban_benar' => '11',
                'nomor_soal' => 2
            ],
            [
                'cabang_lomba_id' => 1,
                'pertanyaan_isian' => 'Berapa akar kuadrat dari 64?',
                'jawaban_benar' => '8',
                'nomor_soal' => 3
            ],
            [
                'cabang_lomba_id' => 1,
                'pertanyaan_isian' => 'Berapa hasil dari 3! (3 faktorial)?',
                'jawaban_benar' => '6',
                'nomor_soal' => 4
            ],
            [
                'cabang_lomba_id' => 1,
                'pertanyaan_isian' => 'Jika sin 30° = x, maka x = ... (dalam pecahan)',
                'jawaban_benar' => '1/2',
                'nomor_soal' => 5
            ],
            [
                'cabang_lomba_id' => 1,
                'pertanyaan_isian' => 'Berapa jumlah sudut dalam segitiga?',
                'jawaban_benar' => '180',
                'nomor_soal' => 6
            ],
            [
                'cabang_lomba_id' => 1,
                'pertanyaan_isian' => 'Berapa nilai π (pi) dengan 2 desimal?',
                'jawaban_benar' => '3.14',
                'nomor_soal' => 7
            ],
            [
                'cabang_lomba_id' => 1,
                'pertanyaan_isian' => 'Jika 2^x = 8, maka x = ...',
                'jawaban_benar' => '3',
                'nomor_soal' => 8
            ],
            [
                'cabang_lomba_id' => 1,
                'pertanyaan_isian' => 'Berapa luas lingkaran dengan jari-jari 5 cm? (gunakan π = 3.14)',
                'jawaban_benar' => '78.5',
                'nomor_soal' => 9
            ],
            [
                'cabang_lomba_id' => 1,
                'pertanyaan_isian' => 'Berapa hasil dari log₁₀ 100?',
                'jawaban_benar' => '2',
                'nomor_soal' => 10
            ],
            [
                'cabang_lomba_id' => 1,
                'pertanyaan_isian' => 'Jika f(x) = 2x + 3, maka f(5) = ...',
                'jawaban_benar' => '13',
                'nomor_soal' => 11
            ],
            [
                'cabang_lomba_id' => 1,
                'pertanyaan_isian' => 'Berapa determinan matriks [[2, 1], [3, 4]]?',
                'jawaban_benar' => '5',
                'nomor_soal' => 12
            ],
            [
                'cabang_lomba_id' => 1,
                'pertanyaan_isian' => 'Berapa suku ke-5 dari barisan aritmatika 2, 5, 8, 11, ...?',
                'jawaban_benar' => '14',
                'nomor_soal' => 13
            ],
            [
                'cabang_lomba_id' => 1,
                'pertanyaan_isian' => 'Berapa rata-rata dari data: 4, 6, 8, 10, 12?',
                'jawaban_benar' => '8',
                'nomor_soal' => 14
            ],
            [
                'cabang_lomba_id' => 1,
                'pertanyaan_isian' => 'Jika cos 60° = x, maka x = ... (dalam pecahan)',
                'jawaban_benar' => '1/2',
                'nomor_soal' => 15
            ],
            
            // Soal Isian Singkat untuk cabang lomba 2 (Fisika)
            [
                'cabang_lomba_id' => 2,
                'pertanyaan_isian' => 'Siapa pengarang novel Laskar Pelangi?',
                'jawaban_benar' => 'Andrea Hirata',
                'nomor_soal' => 1
            ],
            [
                'cabang_lomba_id' => 2,
                'pertanyaan_isian' => 'Apa sinonim dari kata "cantik"?',
                'jawaban_benar' => 'indah',
                'nomor_soal' => 2
            ],
            [
                'cabang_lomba_id' => 2,
                'pertanyaan_isian' => 'Sebutkan ibukota Indonesia!',
                'jawaban_benar' => 'Jakarta',
                'nomor_soal' => 3
            ]
        ];

        foreach ($soalIsianSingkat as $soal) {
            SoalIsianSingkat::create($soal);
        }
    }
}
