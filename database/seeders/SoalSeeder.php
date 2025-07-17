<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SoalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('soal')->insert([
            [
                'cabang_lomba_id' => 1,
                'nomor_soal' => 1,
                'tipe_soal' => 'text',
                'deskripsi_soal' => 'Soal Matematika dasar',
                'pertanyaan' => 'Berapakah hasil dari 2 + 2?',
                'media_soal' => null,
                'opsi_a' => '3',
                'opsi_a_media' => null,
                'opsi_b' => '4',
                'opsi_b_media' => null,
                'opsi_c' => '5',
                'opsi_c_media' => null,
                'opsi_d' => '6',
                'opsi_d_media' => null,
                'opsi_e' => '7',
                'opsi_e_media' => null,
                'jawaban_benar' => 'B',
            ],
            [
                'cabang_lomba_id' => 2,
                'nomor_soal' => 1,
                'tipe_soal' => 'text',
                'deskripsi_soal' => 'Soal Fisika dasar',
                'pertanyaan' => 'Apa satuan SI untuk gaya?',
                'media_soal' => null,
                'opsi_a' => 'Newton',
                'opsi_a_media' => null,
                'opsi_b' => 'Joule',
                'opsi_b_media' => null,
                'opsi_c' => 'Watt',
                'opsi_c_media' => null,
                'opsi_d' => 'Pascal',
                'opsi_d_media' => null,
                'opsi_e' => 'Meter',
                'opsi_e_media' => null,
                'jawaban_benar' => 'A',
            ],
        ]);
    }
}
