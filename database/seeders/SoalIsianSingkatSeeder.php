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
            // Contoh soal untuk cabang lomba 1 (misal: Matematika)
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
            
            // Contoh soal untuk cabang lomba 2 (misal: Bahasa Indonesia)
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
