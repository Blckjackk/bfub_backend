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
                'cabang_lomba_id' => 1,
                'nomor_soal' => 2,
                'tipe_soal' => 'text',
                'deskripsi_soal' => 'Soal aljabar',
                'pertanyaan' => 'Jika x = 3, berapakah nilai dari 2x + 5?',
                'media_soal' => null,
                'opsi_a' => '10',
                'opsi_a_media' => null,
                'opsi_b' => '11',
                'opsi_b_media' => null,
                'opsi_c' => '12',
                'opsi_c_media' => null,
                'opsi_d' => '13',
                'opsi_d_media' => null,
                'opsi_e' => '14',
                'opsi_e_media' => null,
                'jawaban_benar' => 'B',
            ],
            [
                'cabang_lomba_id' => 1,
                'nomor_soal' => 3,
                'tipe_soal' => 'text',
                'deskripsi_soal' => 'Soal geometri',
                'pertanyaan' => 'Luas lingkaran dengan jari-jari 7 cm adalah...',
                'media_soal' => null,
                'opsi_a' => '154 cm²',
                'opsi_a_media' => null,
                'opsi_b' => '144 cm²',
                'opsi_b_media' => null,
                'opsi_c' => '134 cm²',
                'opsi_c_media' => null,
                'opsi_d' => '124 cm²',
                'opsi_d_media' => null,
                'opsi_e' => '114 cm²',
                'opsi_e_media' => null,
                'jawaban_benar' => 'A',
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
            [
                'cabang_lomba_id' => 2,
                'nomor_soal' => 2,
                'tipe_soal' => 'text',
                'deskripsi_soal' => 'Soal Kinematika',
                'pertanyaan' => 'Rumus untuk menghitung kecepatan adalah...',
                'media_soal' => null,
                'opsi_a' => 'v = s/t',
                'opsi_a_media' => null,
                'opsi_b' => 'v = s × t',
                'opsi_b_media' => null,
                'opsi_c' => 'v = t/s',
                'opsi_c_media' => null,
                'opsi_d' => 'v = s + t',
                'opsi_d_media' => null,
                'opsi_e' => 'v = s - t',
                'opsi_e_media' => null,
                'jawaban_benar' => 'A',
            ],
        ]);
    }
}
