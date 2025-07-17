<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jawaban;
use App\Models\JawabanEssay;
use App\Models\Soal;
use Illuminate\Support\Facades\Storage;

class JawabanController extends Controller
{
    // 10. Kirim jawaban pilihan ganda
    public function submitJawabanPG(Request $request)
    {
        $request->validate([
            'peserta_id' => 'required|exists:peserta,id',
            'soal_id' => 'required|exists:soal,id',
            'jawaban_peserta' => 'required|in:A,B,C,D,E'
        ]);

        $soal = Soal::find($request->soal_id);
        $benar = strtoupper($request->jawaban_peserta) === strtoupper($soal->jawaban_benar);

        // Cek apakah sudah pernah menjawab
        $existingJawaban = Jawaban::where('peserta_id', $request->peserta_id)
                                 ->where('soal_id', $request->soal_id)
                                 ->first();

        if ($existingJawaban) {
            // Update jawaban yang sudah ada
            $existingJawaban->update([
                'jawaban_peserta' => strtoupper($request->jawaban_peserta),
                'benar' => $benar,
                'waktu_dijawab' => now()
            ]);
            $jawaban = $existingJawaban;
        } else {
            // Buat jawaban baru
            $jawaban = Jawaban::create([
                'peserta_id' => $request->peserta_id,
                'soal_id' => $request->soal_id,
                'jawaban_peserta' => strtoupper($request->jawaban_peserta),
                'benar' => $benar,
                'waktu_dijawab' => now()
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Jawaban berhasil disimpan',
            'data' => [
                'jawaban' => $jawaban,
                'benar' => $benar
            ]
        ]);
    }

    // 11. Ambil jawaban peserta (indikator terisi)
    public function getJawabanPG(Request $request)
    {
        $request->validate([
            'peserta_id' => 'required|exists:peserta,id',
            'cabang_lomba_id' => 'required|exists:cabang_lomba,id'
        ]);

        $jawaban = Jawaban::with('soal')
                         ->where('peserta_id', $request->peserta_id)
                         ->whereHas('soal', function($query) use ($request) {
                             $query->where('cabang_lomba_id', $request->cabang_lomba_id);
                         })
                         ->get();

        return response()->json([
            'success' => true,
            'message' => 'Jawaban peserta berhasil diambil',
            'data' => $jawaban
        ]);
    }

    // 17. Upload file tugas essay
    public function uploadFileEssay(Request $request)
    {
        $request->validate([
            'peserta_id' => 'required|exists:peserta,id',
            'soal_essay_id' => 'required|exists:soal_essay,id',
            'file' => 'required|file|max:10240|mimes:pdf,doc,docx,txt'
        ]);

        $file = $request->file('file');
        $fileName = 'essay_' . $request->peserta_id . '_' . $request->soal_essay_id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('uploads/essay', $fileName, 'public');

        // Cek apakah sudah pernah upload
        $existingJawaban = JawabanEssay::where('peserta_id', $request->peserta_id)
                                     ->where('soal_essay_id', $request->soal_essay_id)
                                     ->first();

        if ($existingJawaban) {
            // Hapus file lama
            if (Storage::disk('public')->exists($existingJawaban->file_path)) {
                Storage::disk('public')->delete($existingJawaban->file_path);
            }
            
            // Update dengan file baru
            $existingJawaban->update([
                'jawaban_teks' => $request->jawaban_teks ?? '',
                'file_path' => $filePath,
                'file_name' => $fileName
            ]);
            $jawabanEssay = $existingJawaban;
        } else {
            // Buat jawaban baru
            $jawabanEssay = JawabanEssay::create([
                'peserta_id' => $request->peserta_id,
                'soal_essay_id' => $request->soal_essay_id,
                'jawaban_teks' => $request->jawaban_teks ?? '',
                'file_path' => $filePath,
                'file_name' => $fileName
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'File berhasil diupload',
            'data' => $jawabanEssay
        ]);
    }

    // 18. Preview file yang di-upload
    public function previewFileEssay(Request $request)
    {
        $request->validate([
            'peserta_id' => 'required|exists:peserta,id',
            'soal_essay_id' => 'required|exists:soal_essay,id'
        ]);

        $jawabanEssay = JawabanEssay::where('peserta_id', $request->peserta_id)
                                  ->where('soal_essay_id', $request->soal_essay_id)
                                  ->first();

        if (!$jawabanEssay) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'File ditemukan',
            'data' => [
                'file_name' => $jawabanEssay->file_name,
                'file_url' => asset('storage/' . $jawabanEssay->file_path),
                'jawaban_teks' => $jawabanEssay->jawaban_teks,
                'uploaded_at' => $jawabanEssay->created_at ?? $jawabanEssay->updated_at
            ]
        ]);
    }
}
