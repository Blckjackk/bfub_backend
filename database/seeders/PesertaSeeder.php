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
                'kota_provinsi' => 'Jakarta',
                'username' => 'budisantoso',
                'role' => 'peserta',
                'password_hash' => 'peserta123',
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
                'kota_provinsi' => 'Jawa Barat',
                'username' => 'sitiaminah',
                'role' => 'peserta',
                'password_hash' => 'peserta123',
                'cabang_lomba_id' => 2,
                'status_ujian' => 'sedang_ujian',
                'waktu_mulai' => now(),
                'waktu_selesai' => now(),
                'nilai_total' => null,
                'waktu_pengerjaan_total' => null,
            ],
            [
                'nama_lengkap' => 'Ahmad Fauzi',
                'nomor_pendaftaran' => 'KIM-001',
                'asal_sekolah' => 'SMA 3 Surabaya',
                'kota_provinsi' => 'Jawa Timur',
                'username' => 'ahmadfauzi',
                'role' => 'peserta',
                'password_hash' => 'peserta123',
                'cabang_lomba_id' => 1, // Ubah dari 3 ke 1
                'status_ujian' => 'selesai',
                'waktu_mulai' => now(),
                'waktu_selesai' => now(),
                'nilai_total' => 85.50,
                'waktu_pengerjaan_total' => 120,
            ],
            [
                'nama_lengkap' => 'Dewi Lestari',
                'nomor_pendaftaran' => 'BIO-001',
                'asal_sekolah' => 'SMA 4 Medan',
                'kota_provinsi' => 'Sumatera Utara',
                'username' => 'dewilestari',
                'role' => 'peserta',
                'password_hash' => 'peserta123',
                'cabang_lomba_id' => 2, // Ubah dari 4 ke 2
                'status_ujian' => 'belum_mulai',
                'waktu_mulai' => now(),
                'waktu_selesai' => now(),
                'nilai_total' => null,
                'waktu_pengerjaan_total' => null,
            ],
            [
                'nama_lengkap' => 'Rina Susanti',
                'nomor_pendaftaran' => 'MAT-002',
                'asal_sekolah' => 'SMA 5 Yogyakarta',
                'kota_provinsi' => 'DI Yogyakarta',
                'username' => 'rinasusanti',
                'role' => 'peserta',
                'password_hash' => 'peserta123',
                'cabang_lomba_id' => 1,
                'status_ujian' => 'selesai',
                'waktu_mulai' => now(),
                'waktu_selesai' => now(),
                'nilai_total' => 92.75,
                'waktu_pengerjaan_total' => 105,
            ],
        ]);
    }
}
