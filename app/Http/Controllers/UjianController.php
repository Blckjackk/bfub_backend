<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peserta;
use App\Models\Jawaban;
use App\Models\JawabanEssay;

class UjianController extends Controller
{
    // 12. Peserta memulai ujian
    public function mulaiUjian(Request $request)
    {
        $request->validate([
            'peserta_id' => 'required|exists:peserta,id'
        ]);

        $peserta = Peserta::find($request->peserta_id);

        if ($peserta->status_ujian !== 'belum_mulai') {
            return response()->json([
                'success' => false,
                'message' => 'Ujian sudah dimulai atau sudah selesai'
            ], 400);
        }

        $peserta->update([
            'status_ujian' => 'sedang_mengerjakan',
            'waktu_mulai' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ujian dimulai',
            'data' => [
                'peserta' => $peserta,
                'waktu_mulai' => $peserta->waktu_mulai
            ]
        ]);
    }

    // 13. Peserta submit ujian
    public function selesaiUjian(Request $request)
    {
        $request->validate([
            'peserta_id' => 'required|exists:peserta,id'
        ]);

        $peserta = Peserta::find($request->peserta_id);

        if ($peserta->status_ujian !== 'sedang_mengerjakan') {
            return response()->json([
                'success' => false,
                'message' => 'Ujian belum dimulai atau sudah selesai'
            ], 400);
        }

        // Hitung waktu pengerjaan
        $waktuPengerjaan = now()->diffInMinutes($peserta->waktu_mulai);

        // Hitung nilai otomatis untuk pilihan ganda
        $jawabanBenar = Jawaban::where('peserta_id', $request->peserta_id)
                              ->where('benar', true)
                              ->count();

        $totalSoal = Jawaban::where('peserta_id', $request->peserta_id)->count();
        $nilai = $totalSoal > 0 ? ($jawabanBenar / $totalSoal) * 100 : 0;

        $peserta->update([
            'status_ujian' => 'selesai',
            'waktu_selesai' => now(),
            'waktu_pengerjaan_total' => $waktuPengerjaan,
            'nilai_total' => $nilai
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ujian selesai',
            'data' => [
                'peserta' => $peserta,
                'waktu_pengerjaan' => $waktuPengerjaan,
                'nilai_total' => $nilai,
                'jawaban_benar' => $jawabanBenar,
                'total_soal' => $totalSoal
            ]
        ]);
    }

    // 14. Status waktu & pengerjaan
    public function statusUjian(Request $request)
    {
        $request->validate([
            'peserta_id' => 'required|exists:peserta,id'
        ]);

        $peserta = Peserta::with('cabangLomba')->find($request->peserta_id);

        $waktuSekarang = now();
        $waktuSisa = null;
        $statusWaktu = 'belum_mulai';

        if ($peserta->status_ujian === 'sedang_mengerjakan' && $peserta->waktu_mulai) {
            $waktuBerakhir = $peserta->cabangLomba->waktu_akhir_pengerjaan;
            $waktuSisa = $waktuSekarang->diffInMinutes($waktuBerakhir, false);
            $statusWaktu = $waktuSisa > 0 ? 'berjalan' : 'habis';
        }

        return response()->json([
            'success' => true,
            'message' => 'Status ujian',
            'data' => [
                'status_ujian' => $peserta->status_ujian,
                'waktu_mulai' => $peserta->waktu_mulai,
                'waktu_selesai' => $peserta->waktu_selesai,
                'waktu_sisa_menit' => $waktuSisa,
                'status_waktu' => $statusWaktu,
                'waktu_pengerjaan_total' => $peserta->waktu_pengerjaan_total,
                'nilai_total' => $peserta->nilai_total
            ]
        ]);
    }

    // 15. Auto save jawaban
    public function autoSave(Request $request)
    {
        $request->validate([
            'peserta_id' => 'required|exists:peserta,id',
            'jawaban_data' => 'required|array'
        ]);

        $saved = [];
        $errors = [];

        foreach ($request->jawaban_data as $jawaban) {
            try {
                if (isset($jawaban['soal_id']) && isset($jawaban['jawaban_peserta'])) {
                    $existingJawaban = Jawaban::where('peserta_id', $request->peserta_id)
                                             ->where('soal_id', $jawaban['soal_id'])
                                             ->first();

                    if ($existingJawaban) {
                        $existingJawaban->update([
                            'jawaban_peserta' => strtoupper($jawaban['jawaban_peserta']),
                            'waktu_dijawab' => now()
                        ]);
                        $saved[] = $existingJawaban;
                    }
                }
            } catch (\Exception $e) {
                $errors[] = [
                    'soal_id' => $jawaban['soal_id'] ?? 'unknown',
                    'error' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Auto save completed',
            'data' => [
                'saved_count' => count($saved),
                'error_count' => count($errors),
                'errors' => $errors
            ]
        ]);
    }

    // 28. Endpoint simulasi ujian
    public function simulasiTest(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Simulasi ujian aktif',
            'data' => [
                'simulasi_mode' => true,
                'waktu_simulasi' => now()->addHours(2),
                'instruksi' => 'Ini adalah mode simulasi ujian untuk testing'
            ]
        ]);
    }
}
