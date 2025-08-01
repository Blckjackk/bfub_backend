<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Peserta;
use App\Models\CabangLomba;
use App\Models\Soal;
use App\Models\SoalEssay;
use App\Models\SoalIsianSingkat;
use App\Models\Jawaban;
use App\Models\JawabanEssay;
use App\Models\JawabanIsianSingkat;
use Illuminate\Support\Facades\Hash;

class PesertaLengkapSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil cabang lomba matematika
        $cabangLombaMatematika = CabangLomba::where('nama_lomba', 'Matematika')->first();
        
        if (!$cabangLombaMatematika) {
            $this->command->error('Cabang lomba Matematika tidak ditemukan!');
            return;
        }

        // Data peserta contoh
        $pesertaData = [
            [
                'nama_lengkap' => 'Ahmad Rizki Pratama',
                'nomor_pendaftaran' => 'MAT2025001',
                'asal_sekolah' => 'SMA Negeri 1 Jakarta',
                'kota_provinsi' => 'Jakarta',
                'username' => 'ahmad_rizki',
                'password' => 'password123'
            ],
            [
                'nama_lengkap' => 'Siti Nurhaliza',
                'nomor_pendaftaran' => 'MAT2025002',
                'asal_sekolah' => 'SMA Negeri 2 Bandung',
                'kota_provinsi' => 'Bandung, Jawa Barat',
                'username' => 'siti_nurhaliza',
                'password' => 'password123'
            ],
            [
                'nama_lengkap' => 'Bayu Adi Saputra',
                'nomor_pendaftaran' => 'MAT2025003',
                'asal_sekolah' => 'SMA Negeri 1 Surabaya',
                'kota_provinsi' => 'Surabaya, Jawa Timur',
                'username' => 'bayu_adi',
                'password' => 'password123'
            ]
        ];

        foreach ($pesertaData as $data) {
            // Buat peserta
            $peserta = Peserta::create([
                'nama_lengkap' => $data['nama_lengkap'],
                'nomor_pendaftaran' => $data['nomor_pendaftaran'],
                'asal_sekolah' => $data['asal_sekolah'],
                'kota_provinsi' => $data['kota_provinsi'],
                'username' => $data['username'],
                'role' => 'peserta',
                'password_hash' => Hash::make($data['password']),
                'cabang_lomba_id' => $cabangLombaMatematika->id,
                'status_ujian' => 'selesai',
                'waktu_mulai' => now()->subHours(2),
                'waktu_selesai' => now()->subHour(1),
                'nilai_total' => 0, // Will be calculated
                'waktu_pengerjaan_total' => '01:00:00'
            ]);

            $this->command->info("Created peserta: {$peserta->nama_lengkap}");

            // Ambil semua soal untuk cabang lomba matematika
            $soalPG = Soal::where('cabang_lomba_id', $cabangLombaMatematika->id)->get();
            $soalEssay = SoalEssay::where('cabang_lomba_id', $cabangLombaMatematika->id)->get();
            $soalIsianSingkat = SoalIsianSingkat::where('cabang_lomba_id', $cabangLombaMatematika->id)->get();

            $totalNilai = 0;

            // Isi jawaban PG (random benar/salah dengan probabilitas 70% benar)
            foreach ($soalPG as $soal) {
                $pilihanJawaban = ['A', 'B', 'C', 'D'];
                $jawabanPeserta = $pilihanJawaban[array_rand($pilihanJawaban)];
                $benar = rand(1, 100) <= 70 ? ($jawabanPeserta === $soal->jawaban_benar) : false;
                
                // Override untuk memastikan ada jawaban benar dan salah
                if (rand(1, 100) <= 70) {
                    $jawabanPeserta = $soal->jawaban_benar;
                    $benar = true;
                }

                Jawaban::create([
                    'peserta_id' => $peserta->id,
                    'soal_id' => $soal->id,
                    'jawaban_peserta' => $jawabanPeserta,
                    'benar' => $benar,
                    'waktu_dijawab' => now()->subMinutes(rand(30, 120))
                ]);

                if ($benar) {
                    $totalNilai += 1; // Setiap soal PG benar = 1 poin
                }
            }

            // Isi jawaban Essay
            $essayAnswers = [
                'Limit adalah nilai yang didekati suatu fungsi ketika variabel independennya mendekati suatu nilai tertentu.',
                'Turunan merupakan laju perubahan sesaat dari suatu fungsi terhadap variabelnya.',
                'Integral adalah operasi kebalikan dari diferensiasi yang digunakan untuk mencari luas daerah.',
                'Matriks adalah susunan bilangan dalam bentuk persegi panjang yang terdiri dari baris dan kolom.',
                'Vektor adalah besaran yang memiliki besar dan arah, dapat direpresentasikan dalam koordinat kartesian.',
                'Fungsi trigonometri menghubungkan sudut dalam segitiga siku-siku dengan perbandingan sisi-sisinya.',
                'Logaritma adalah operasi kebalikan dari eksponen, digunakan untuk mencari pangkat dari bilangan pokok.',
                'Statistika deskriptif merangkum dan menggambarkan karakteristik data dalam bentuk numerik.',
                'Peluang adalah ukuran kemungkinan terjadinya suatu kejadian dalam eksperimen acak.',
                'Geometri analitik menggunakan koordinat untuk mempelajari sifat-sifat bangun geometri.'
            ];

            foreach ($soalEssay as $index => $soal) {
                $jawabanTeks = $essayAnswers[$index % count($essayAnswers)];
                $score = rand(6, 10); // Random score antara 6-10 dari bobot 10

                JawabanEssay::create([
                    'peserta_id' => $peserta->id,
                    'soal_essay_id' => $soal->id,
                    'jawaban_teks' => $jawabanTeks,
                    'score' => $score
                ]);

                $totalNilai += $score;
            }

            // Isi jawaban Isian Singkat
            $isianAnswers = [
                '42', '√2', 'π', '∞', '0', 
                '1/2', '2π', 'e', '90°', '180°',
                '1', '2', '3', '4', '5'
            ];

            foreach ($soalIsianSingkat as $index => $soal) {
                $jawabanPeserta = $isianAnswers[$index % count($isianAnswers)];
                $benar = rand(1, 100) <= 60; // 60% kemungkinan benar
                $score = $benar ? 5 : rand(0, 3); // Jika benar = 5, jika salah = 0-3

                JawabanIsianSingkat::create([
                    'peserta_id' => $peserta->id,
                    'soal_isian_singkat_id' => $soal->id,
                    'jawaban_peserta' => $jawabanPeserta,
                    'benar' => $benar,
                    'score' => $score,
                    'waktu_dijawab' => now()->subMinutes(rand(30, 120))
                ]);

                $totalNilai += $score;
            }

            // Update nilai total peserta
            $peserta->update(['nilai_total' => min($totalNilai, 100)]); // Max 100
            
            $this->command->info("  - PG answers: {$soalPG->count()}");
            $this->command->info("  - Essay answers: {$soalEssay->count()}");
            $this->command->info("  - Isian singkat answers: {$soalIsianSingkat->count()}");
            $this->command->info("  - Total nilai: {$peserta->nilai_total}");
            $this->command->info("");
        }

        $this->command->info('Seeder PesertaLengkap selesai!');
    }
}
