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
        // Generate 100 soal untuk cabang lomba ID 1
        $soalData = [];
        
        // Topik-topik matematika untuk variasi soal
        $topiks = [
            'Aritmatika', 'Aljabar', 'Geometri', 'Trigonometri', 'Statistika',
            'Peluang', 'Fungsi', 'Persamaan', 'Pertidaksamaan', 'Logaritma',
            'Eksponen', 'Deret', 'Matriks', 'Vektor', 'Limit'
        ];

        for ($i = 1; $i <= 100; $i++) {
            $topik = $topiks[($i - 1) % count($topiks)];
            
            // Generate soal berdasarkan nomor dan topik
            $soal = $this->generateSoal($i, $topik);
            
            $soalData[] = [
                'cabang_lomba_id' => 1,
                'nomor_soal' => $i,
                'tipe_soal' => 'text',
                'deskripsi_soal' => "Soal {$topik} nomor {$i}",
                'pertanyaan' => $soal['pertanyaan'],
                'media_soal' => null,
                'opsi_a' => $soal['opsi_a'],
                'opsi_a_media' => null,
                'opsi_b' => $soal['opsi_b'],
                'opsi_b_media' => null,
                'opsi_c' => $soal['opsi_c'],
                'opsi_c_media' => null,
                'opsi_d' => $soal['opsi_d'],
                'opsi_d_media' => null,
                'opsi_e' => $soal['opsi_e'],
                'opsi_e_media' => null,
                'jawaban_benar' => $soal['jawaban_benar'],
            ];
        }

        // Insert data dalam batch untuk performa yang lebih baik
        $chunks = array_chunk($soalData, 25);
        foreach ($chunks as $chunk) {
            DB::table('soal')->insert($chunk);
        }

        // Tambahkan juga beberapa soal untuk cabang lomba lain
        DB::table('soal')->insert([
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

    private function generateSoal($nomor, $topik)
    {
        $soalTemplates = [
            'Aritmatika' => [
                'pertanyaan' => "Hasil dari {a} + {b} - {c} adalah...",
                'jawaban' => ['5', '10', '15', '20', '25'],
                'benar' => 'B'
            ],
            'Aljabar' => [
                'pertanyaan' => "Jika x = {a}, maka nilai dari {b}x + {c} adalah...",
                'jawaban' => ['8', '12', '16', '20', '24'],
                'benar' => 'C'
            ],
            'Geometri' => [
                'pertanyaan' => "Luas persegi dengan sisi {a} cm adalah...",
                'jawaban' => ['16 cm²', '25 cm²', '36 cm²', '49 cm²', '64 cm²'],
                'benar' => 'C'
            ],
            'Trigonometri' => [
                'pertanyaan' => "Nilai sin 30° adalah...",
                'jawaban' => ['1/2', '1/3', '√2/2', '√3/2', '1'],
                'benar' => 'A'
            ],
            'Statistika' => [
                'pertanyaan' => "Rata-rata dari data {a}, {b}, {c}, {d} adalah...",
                'jawaban' => ['5', '6', '7', '8', '9'],
                'benar' => 'C'
            ],
            'Peluang' => [
                'pertanyaan' => "Peluang munculnya angka genap pada pelemparan dadu adalah...",
                'jawaban' => ['1/6', '1/3', '1/2', '2/3', '5/6'],
                'benar' => 'C'
            ],
            'Fungsi' => [
                'pertanyaan' => "Jika f(x) = {a}x + {b}, maka f({c}) adalah...",
                'jawaban' => ['10', '15', '20', '25', '30'],
                'benar' => 'D'
            ],
            'Persamaan' => [
                'pertanyaan' => "Penyelesaian dari {a}x + {b} = {c} adalah...",
                'jawaban' => ['x = 2', 'x = 3', 'x = 4', 'x = 5', 'x = 6'],
                'benar' => 'B'
            ],
            'Pertidaksamaan' => [
                'pertanyaan' => "Himpunan penyelesaian dari {a}x > {b} adalah...",
                'jawaban' => ['x > 2', 'x > 3', 'x > 4', 'x > 5', 'x > 6'],
                'benar' => 'A'
            ],
            'Logaritma' => [
                'pertanyaan' => "Nilai dari log {a} adalah...",
                'jawaban' => ['1', '2', '3', '4', '5'],
                'benar' => 'B'
            ],
            'Eksponen' => [
                'pertanyaan' => "Hasil dari {a}^{b} adalah...",
                'jawaban' => ['8', '16', '32', '64', '128'],
                'benar' => 'C'
            ],
            'Deret' => [
                'pertanyaan' => "Suku ke-{a} dari barisan aritmatika 2, 5, 8, 11, ... adalah...",
                'jawaban' => ['14', '17', '20', '23', '26'],
                'benar' => 'D'
            ],
            'Matriks' => [
                'pertanyaan' => "Determinan matriks [[{a}, {b}], [{c}, {d}]] adalah...",
                'jawaban' => ['0', '1', '2', '3', '4'],
                'benar' => 'C'
            ],
            'Vektor' => [
                'pertanyaan' => "Panjang vektor ({a}, {b}) adalah...",
                'jawaban' => ['3', '4', '5', '6', '7'],
                'benar' => 'C'
            ],
            'Limit' => [
                'pertanyaan' => "Nilai limit x→{a} dari ({b}x + {c}) adalah...",
                'jawaban' => ['5', '10', '15', '20', '25'],
                'benar' => 'B'
            ]
        ];

        // Ambil template berdasarkan topik
        $template = $soalTemplates[$topik] ?? $soalTemplates['Aritmatika'];
        
        // Generate nilai random untuk variabel
        $a = rand(1, 10);
        $b = rand(1, 10);
        $c = rand(1, 10);
        $d = rand(1, 10);

        // Replace placeholder dengan nilai
        $pertanyaan = str_replace(['{a}', '{b}', '{c}', '{d}'], [$a, $b, $c, $d], $template['pertanyaan']);
        
        // Tambahkan variasi berdasarkan nomor soal
        if ($nomor % 10 == 0) {
            $pertanyaan .= " (Soal bonus!)";
        }

        return [
            'pertanyaan' => $pertanyaan,
            'opsi_a' => $template['jawaban'][0],
            'opsi_b' => $template['jawaban'][1],
            'opsi_c' => $template['jawaban'][2],
            'opsi_d' => $template['jawaban'][3],
            'opsi_e' => $template['jawaban'][4],
            'jawaban_benar' => $template['benar']
        ];
    }
}
