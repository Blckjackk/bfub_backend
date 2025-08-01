<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SoalIsianSingkat;
use App\Models\JawabanIsianSingkat;

class IsianSingkatController extends Controller
{
    // Ambil semua soal isian singkat
    public function getSoalIsianSingkat(Request $request)
    {
        $request->validate([
            'cabang_lomba_id' => 'required|exists:cabang_lomba,id'
        ]);

        $soal = SoalIsianSingkat::where('cabang_lomba_id', $request->cabang_lomba_id)
                               ->orderBy('nomor_soal')
                               ->get();

        return response()->json([
            'success' => true,
            'message' => 'Soal isian singkat berhasil diambil',
            'data' => $soal
        ]);
    }

    // Ambil soal isian singkat berdasarkan nomor
    public function getSoalIsianSingkatByNomor(Request $request, $nomor)
    {
        $request->validate([
            'cabang_lomba_id' => 'required|exists:cabang_lomba,id'
        ]);

        $soal = SoalIsianSingkat::where('cabang_lomba_id', $request->cabang_lomba_id)
                               ->where('nomor_soal', $nomor)
                               ->first();

        if (!$soal) {
            return response()->json([
                'success' => false,
                'message' => 'Soal tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Soal berhasil diambil',
            'data' => $soal
        ]);
    }

    // Kirim jawaban isian singkat (bulk)
    public function submitJawabanIsianSingkat(Request $request)
    {
        $request->validate([
            'answers' => 'required|array',
            'answers.*.peserta_id' => 'required|exists:peserta,id',
            'answers.*.soal_isian_singkat_id' => 'required|exists:soal_isian_singkat,id',
            'answers.*.jawaban_peserta' => 'nullable|string',
            'answers.*.waktu_dijawab' => 'required|string'
        ]);

        $savedAnswers = [];

        foreach ($request->answers as $answerData) {
            $soal = SoalIsianSingkat::find($answerData['soal_isian_singkat_id']);
            
            // Handle null/empty answers - varchar field can accept null
            $jawabanPeserta = !empty($answerData['jawaban_peserta']) ? $answerData['jawaban_peserta'] : null;
            $benar = false;
            
            if ($jawabanPeserta && $soal && $soal->jawaban_benar) {
                // Cek kebenaran jawaban (case insensitive dan trim whitespace)
                $jawabanBenar = strtolower(trim($soal->jawaban_benar));
                $jawabanPesertaTrimmed = strtolower(trim($jawabanPeserta));
                $benar = $jawabanBenar === $jawabanPesertaTrimmed;
            }

            // Convert datetime format from frontend to MySQL format
            $waktuDijawab = $answerData['waktu_dijawab'];

            // Cek apakah sudah pernah menjawab
            $existingJawaban = JawabanIsianSingkat::where('peserta_id', $answerData['peserta_id'])
                                                ->where('soal_isian_singkat_id', $answerData['soal_isian_singkat_id'])
                                                ->first();

            if ($existingJawaban) {
                // Update jawaban yang sudah ada
                $existingJawaban->update([
                    'jawaban_peserta' => $jawabanPeserta,
                    'benar' => $benar,
                    'waktu_dijawab' => $waktuDijawab
                ]);
                $savedAnswers[] = $existingJawaban;
            } else {
                // Buat jawaban baru
                $jawaban = JawabanIsianSingkat::create([
                    'peserta_id' => $answerData['peserta_id'],
                    'soal_isian_singkat_id' => $answerData['soal_isian_singkat_id'],
                    'jawaban_peserta' => $jawabanPeserta,
                    'benar' => $benar,
                    'waktu_dijawab' => $waktuDijawab
                ]);
                $savedAnswers[] = $jawaban;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Semua jawaban isian singkat berhasil disimpan',
            'data' => $savedAnswers
        ]);
    }

    // Ambil jawaban isian singkat peserta
    public function getJawabanIsianSingkat(Request $request)
    {
        $request->validate([
            'peserta_id' => 'required|exists:peserta,id',
            'cabang_lomba_id' => 'required|exists:cabang_lomba,id'
        ]);

        $jawaban = JawabanIsianSingkat::with('soalIsianSingkat')
                                     ->where('peserta_id', $request->peserta_id)
                                     ->whereHas('soalIsianSingkat', function($query) use ($request) {
                                         $query->where('cabang_lomba_id', $request->cabang_lomba_id);
                                     })
                                     ->get();

        return response()->json([
            'success' => true,
            'message' => 'Jawaban isian singkat peserta berhasil diambil',
            'data' => $jawaban
        ]);
    }

    // Preview jawaban isian singkat
    public function previewJawabanIsianSingkat(Request $request)
    {
        $request->validate([
            'peserta_id' => 'required|exists:peserta,id',
            'soal_isian_singkat_id' => 'required|exists:soal_isian_singkat,id'
        ]);

        $jawaban = JawabanIsianSingkat::with('soalIsianSingkat')
                                     ->where('peserta_id', $request->peserta_id)
                                     ->where('soal_isian_singkat_id', $request->soal_isian_singkat_id)
                                     ->first();

        if (!$jawaban) {
            return response()->json([
                'success' => false,
                'message' => 'Jawaban tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Jawaban ditemukan',
            'data' => [
                'jawaban_peserta' => $jawaban->jawaban_peserta,
                'benar' => $jawaban->benar,
                'waktu_dijawab' => $jawaban->waktu_dijawab,
                'soal' => $jawaban->soalIsianSingkat
            ]
        ]);
    }
}
