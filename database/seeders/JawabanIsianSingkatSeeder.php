<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JawabanIsianSingkat;

class JawabanIsianSingkatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jawabanIsianSingkat = [
            // Contoh jawaban peserta untuk soal isian singkat
            [
                'peserta_id' => 1,
                'soal_isian_singkat_id' => 1,
                'jawaban_peserta' => '42',
                'benar' => true,
                'waktu_dijawab' => now()
            ],
            [
                'peserta_id' => 1,
                'soal_isian_singkat_id' => 2,
                'jawaban_peserta' => '11',
                'benar' => true,
                'waktu_dijawab' => now()
            ],
            [
                'peserta_id' => 2,
                'soal_isian_singkat_id' => 1,
                'jawaban_peserta' => '40',
                'benar' => false,
                'waktu_dijawab' => now()
            ],
            [
                'peserta_id' => 2,
                'soal_isian_singkat_id' => 4,
                'jawaban_peserta' => 'Andrea Hirata',
                'benar' => true,
                'waktu_dijawab' => now()
            ]
        ];

        foreach ($jawabanIsianSingkat as $jawaban) {
            JawabanIsianSingkat::create($jawaban);
        }
    }
}
