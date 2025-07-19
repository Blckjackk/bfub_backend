<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peserta;
use App\Models\Soal;
use App\Models\SoalEssay;
use App\Models\SoalIsianSingkat;
use App\Models\Jawaban;
use App\Models\JawabanEssay;
use App\Models\JawabanIsianSingkat;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    // 20. Daftar peserta (admin panel)
    public function getPeserta(Request $request)
    {
        $query = Peserta::with('cabangLomba');

        // 21. Filter peserta by cabang
        if ($request->has('cabang')) {
            $query->whereHas('cabangLomba', function($q) use ($request) {
                $q->where('nama_cabang', 'like', '%' . $request->cabang . '%');
            });
        }

        $peserta = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar peserta',
            'data' => $peserta
        ]);
    }

    // 22. Tambah soal pilihan ganda
    public function tambahSoalPG(Request $request)
    {
        $request->validate([
            'cabang_lomba_id' => 'required|exists:cabang_lomba,id',
            'nomor_soal' => 'required|integer',
            'tipe_soal' => 'required|in:text,gambar',
            'deskripsi_soal' => 'nullable|string',
            'pertanyaan' => 'required|string',
            'media_soal' => 'nullable|string',
            'opsi_a' => 'required|string',
            'opsi_b' => 'required|string',
            'opsi_c' => 'required|string',
            'opsi_d' => 'required|string',
            'opsi_e' => 'required|string',
            'jawaban_benar' => 'required|in:A,B,C,D,E'
        ]);

        $soal = Soal::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Soal pilihan ganda berhasil ditambahkan',
            'data' => $soal
        ], 201);
    }

    // 23. Tambah soal essay
    public function tambahSoalEssay(Request $request)
    {
        $request->validate([
            'cabang_lomba_id' => 'required|exists:cabang_lomba,id',
            'pertanyaan_essay' => 'required|string'
        ]);

        $soalEssay = SoalEssay::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Soal essay berhasil ditambahkan',
            'data' => $soalEssay
        ], 201);
    }

    // 24. Lihat semua jawaban peserta
    public function getJawabanPeserta(Request $request)
    {
        $jawabanPG = Jawaban::with(['peserta', 'soal'])->get();
        $jawabanEssay = JawabanEssay::with(['peserta', 'soalEssay'])->get();

        return response()->json([
            'success' => true,
            'message' => 'Semua jawaban peserta',
            'data' => [
                'jawaban_pilihan_ganda' => $jawabanPG,
                'jawaban_essay' => $jawabanEssay
            ]
        ]);
    }

    // 25. Hitung nilai otomatis PG
    public function hitungNilaiOtomatis(Request $request)
    {
        $peserta = Peserta::with(['jawaban.soal', 'cabangLomba'])->get();
        $hasil = [];

        foreach ($peserta as $p) {
            $jawabanBenar = $p->jawaban->where('benar', true)->count();
            $totalSoal = $p->jawaban->count();
            $nilai = $totalSoal > 0 ? ($jawabanBenar / $totalSoal) * 100 : 0;

            // Update nilai di database
            $p->update(['nilai_total' => $nilai]);

            $hasil[] = [
                'peserta' => $p->nama_lengkap,
                'nomor_pendaftaran' => $p->nomor_pendaftaran,
                'cabang_lomba' => $p->cabangLomba->nama_cabang,
                'jawaban_benar' => $jawabanBenar,
                'total_soal' => $totalSoal,
                'nilai' => $nilai
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Nilai otomatis berhasil dihitung',
            'data' => $hasil
        ]);
    }

    // 26. Export nilai peserta ke Excel
    public function exportExcel(Request $request)
    {
        $peserta = Peserta::with('cabangLomba')->get();
        
        $csvData = "Nama,Nomor Pendaftaran,Asal Sekolah,Cabang Lomba,Status Ujian,Nilai Total\n";
        
        foreach ($peserta as $p) {
            $csvData .= sprintf(
                "%s,%s,%s,%s,%s,%s\n",
                $p->nama_lengkap,
                $p->nomor_pendaftaran,
                $p->asal_sekolah,
                $p->cabangLomba->nama_cabang ?? '',
                $p->status_ujian,
                $p->nilai_total ?? 0
            );
        }

        $fileName = 'nilai_peserta_' . date('Y-m-d_H-i-s') . '.csv';
        Storage::disk('public')->put('exports/' . $fileName, $csvData);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diexport',
            'data' => [
                'file_name' => $fileName,
                'download_url' => asset('storage/exports/' . $fileName)
            ]
        ]);
    }

    // 27. Download semua file upload
    public function downloadFiles(Request $request)
    {
        $files = JawabanEssay::whereNotNull('file_path')->get();
        
        $fileList = [];
        foreach ($files as $file) {
            if (Storage::disk('public')->exists($file->file_path)) {
                $fileList[] = [
                    'peserta' => $file->peserta->nama_lengkap,
                    'file_name' => $file->file_name,
                    'download_url' => asset('storage/' . $file->file_path),
                    'uploaded_at' => $file->created_at ?? $file->updated_at
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Daftar file upload',
            'data' => $fileList
        ]);
    }

    // 28. Tambah soal isian singkat
    public function tambahSoalIsianSingkat(Request $request)
    {
        $request->validate([
            'cabang_lomba_id' => 'required|exists:cabang_lomba,id',
            'pertanyaan_isian' => 'required|string',
            'jawaban_benar' => 'required|string',
            'nomor_soal' => 'required|integer'
        ]);

        $soal = SoalIsianSingkat::create([
            'cabang_lomba_id' => $request->cabang_lomba_id,
            'pertanyaan_isian' => $request->pertanyaan_isian,
            'jawaban_benar' => $request->jawaban_benar,
            'nomor_soal' => $request->nomor_soal
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Soal isian singkat berhasil ditambahkan',
            'data' => $soal
        ]);
    }
}
