<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PesertaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('peserta')->insert([
            [
                'nama_lengkap' => 'Budi Santoso',
                'nomor_pendaftaran' => 'MAT-001',
                'asal_sekolah' => 'SMA 1 Jakarta',
                'email' => 'budi@example.com',
                'password_hash' => bcrypt('peserta123'),
                'cabang_lomba_id' => 1,
                'status_ujian' => 'belum_mulai',
                'waktu_mulai' => now(),
                'waktu_selesai' => now(),
                'nilai_total' => null,
                'waktu_pengerjaan_total' => null,
            ],
            [
                'nama_lengkap' => 'Siti Aminah',
                'nomor_pendaftaran' => 'FIS-001',
                'asal_sekolah' => 'SMA 2 Bandung',
                'email' => 'siti@example.com',
                'password_hash' => bcrypt('peserta123'),
                'cabang_lomba_id' => 2,
                'status_ujian' => 'belum_mulai',
                'waktu_mulai' => now(),
                'waktu_selesai' => now(),
                'nilai_total' => null,
                'waktu_pengerjaan_total' => null,
            ],
        ]);
    }
}
