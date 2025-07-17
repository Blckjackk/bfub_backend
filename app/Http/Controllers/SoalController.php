<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Soal;
use App\Models\SoalEssay;

class SoalController extends Controller
{
    // 8. Ambil semua soal pilihan ganda
    public function getSoalPG(Request $request)
    {
        $request->validate([
            'cabang_lomba_id' => 'required|exists:cabang_lomba,id'
        ]);

        $soal = Soal::where('cabang_lomba_id', $request->cabang_lomba_id)
                   ->orderBy('nomor_soal')
                   ->get();

        return response()->json([
            'success' => true,
            'message' => 'Soal pilihan ganda berhasil diambil',
            'data' => $soal
        ]);
    }

    // 9. Ambil soal PG berdasarkan nomor
    public function getSoalPGByNomor(Request $request, $nomor)
    {
        $request->validate([
            'cabang_lomba_id' => 'required|exists:cabang_lomba,id'
        ]);

        $soal = Soal::where('cabang_lomba_id', $request->cabang_lomba_id)
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

    // 16. Ambil soal essay
    public function getSoalEssay(Request $request)
    {
        $request->validate([
            'cabang_lomba_id' => 'required|exists:cabang_lomba,id'
        ]);

        $soalEssay = SoalEssay::where('cabang_lomba_id', $request->cabang_lomba_id)
                             ->get();

        return response()->json([
            'success' => true,
            'message' => 'Soal essay berhasil diambil',
            'data' => $soalEssay
        ]);
    }
}
