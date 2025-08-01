<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jawaban;
use App\Models\JawabanEssay;
use App\Models\Soal;
use Illuminate\Support\Facades\Storage;

class JawabanController extends Controller
{
    // 10. Kirim jawaban pilihan ganda (bulk)
    public function submitJawabanPG(Request $request)
    {
        $request->validate([
            'answers' => 'required|array',
            'answers.*.peserta_id' => 'required|exists:peserta,id',
            'answers.*.soal_id' => 'required|exists:soal,id',
            'answers.*.jawaban_peserta' => 'nullable|string|max:1',
            'answers.*.waktu_dijawab' => 'required|string'
        ]);

        $savedAnswers = [];

        foreach ($request->answers as $answerData) {
            $soal = Soal::find($answerData['soal_id']);
            
            // Handle null/empty answers - use null for char field that's nullable
            $jawabanPeserta = !empty($answerData['jawaban_peserta']) ? $answerData['jawaban_peserta'] : null;
            $benar = false;
            
            if ($jawabanPeserta && $soal && $soal->jawaban_benar) {
                $benar = strtolower($jawabanPeserta) === strtolower($soal->jawaban_benar);
            }

            // Convert datetime format from frontend to MySQL format
            $waktuDijawab = $answerData['waktu_dijawab'];
            
            // Cek apakah sudah pernah menjawab
            $existingJawaban = Jawaban::where('peserta_id', $answerData['peserta_id'])
                                     ->where('soal_id', $answerData['soal_id'])
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
                $jawaban = Jawaban::create([
                    'peserta_id' => $answerData['peserta_id'],
                    'soal_id' => $answerData['soal_id'],
                    'jawaban_peserta' => $jawabanPeserta,
                    'benar' => $benar,
                    'waktu_dijawab' => $waktuDijawab
                ]);
                $savedAnswers[] = $jawaban;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Semua jawaban PG berhasil disimpan',
            'data' => $savedAnswers
        ]);
    }

    // Submit jawaban essay text (bukan file)
    public function submitJawabanEssay(Request $request)
    {
        $request->validate([
            'answers' => 'required|array',
            'answers.*.peserta_id' => 'required|exists:peserta,id',
            'answers.*.soal_essay_id' => 'required|exists:soal_essay,id',
            'answers.*.jawaban_teks' => 'nullable|string'
        ]);

        $savedAnswers = [];

        foreach ($request->answers as $answerData) {
            // Handle null/empty answers
            $jawabanTeks = $answerData['jawaban_teks'] ?? null;
            
            // Cek apakah sudah pernah menjawab
            $existingJawaban = JawabanEssay::where('peserta_id', $answerData['peserta_id'])
                                         ->where('soal_essay_id', $answerData['soal_essay_id'])
                                         ->first();

            if ($existingJawaban) {
                // Update jawaban yang sudah ada
                $existingJawaban->update([
                    'jawaban_teks' => $jawabanTeks
                ]);
                $savedAnswers[] = $existingJawaban;
            } else {
                // Buat jawaban baru
                $jawaban = JawabanEssay::create([
                    'peserta_id' => $answerData['peserta_id'],
                    'soal_essay_id' => $answerData['soal_essay_id'],
                    'jawaban_teks' => $jawabanTeks
                ]);
                $savedAnswers[] = $jawaban;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Semua jawaban essay berhasil disimpan',
            'data' => $savedAnswers
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
