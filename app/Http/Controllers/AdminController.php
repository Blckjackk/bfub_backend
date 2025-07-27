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
use App\Models\CabangLomba;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    // 1. Get all lomba data untuk halaman manajemen lomba
    public function getLomba(Request $request)
    {
        try {
            $query = CabangLomba::with(['soal', 'soalEssay', 'soalIsianSingkat', 'peserta']);

            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama_cabang', 'like', '%' . $search . '%')
                      ->orWhere('deskripsi_lomba', 'like', '%' . $search . '%');
                });
            }

            // Get all lomba with statistics
            $lomba = $query->get();

            // Calculate statistics for each lomba
            $lombaData = $lomba->map(function($item) {
                return [
                    'id' => $item->id,
                    'nama_cabang' => $item->nama_cabang,
                    'deskripsi_lomba' => $item->deskripsi_lomba,
                    'waktu_mulai_pengerjaan' => $item->waktu_mulai_pengerjaan,
                    'waktu_akhir_pengerjaan' => $item->waktu_akhir_pengerjaan,
                    'total_soal_pg' => $item->soal->count(),
                    'total_soal_essay' => $item->soalEssay->count(),
                    'total_soal_isian' => $item->soalIsianSingkat->count(),
                    'total_peserta' => $item->peserta->count(),
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Data lomba berhasil diambil',
                'data' => $lombaData,
                'total' => $lombaData->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data lomba',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 2. Get lomba by ID dengan detail soal
    public function getLombaById($id)
    {
        try {
            $lomba = CabangLomba::with(['soal', 'soalEssay', 'soalIsianSingkat', 'peserta'])->find($id);

            if (!$lomba) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lomba tidak ditemukan'
                ], 404);
            }

            // Format data dengan detail soal
            $data = [
                'lomba' => [
                    'id' => $lomba->id,
                    'nama_cabang' => $lomba->nama_cabang,
                    'deskripsi_lomba' => $lomba->deskripsi_lomba,
                    'waktu_mulai_pengerjaan' => $lomba->waktu_mulai_pengerjaan,
                    'waktu_akhir_pengerjaan' => $lomba->waktu_akhir_pengerjaan
                ],
                'soal_pg' => $lomba->soal->map(function($soal) {
                    return [
                        'id' => $soal->id,
                        'nomor_soal' => $soal->nomor_soal,
                        'pertanyaan' => $soal->pertanyaan,
                        'opsi_a' => $soal->opsi_a,
                        'opsi_b' => $soal->opsi_b,
                        'opsi_c' => $soal->opsi_c,
                        'opsi_d' => $soal->opsi_d,
                        'opsi_e' => $soal->opsi_e,
                        'jawaban_benar' => $soal->jawaban_benar,
                        'tipe_soal' => $soal->tipe_soal,
                        'deskripsi_soal' => $soal->deskripsi_soal
                    ];
                }),
                'soal_essay' => $lomba->soalEssay->map(function($soal) {
                    return [
                        'id' => $soal->id,
                        'nomor_soal' => $soal->nomor_soal,
                        'pertanyaan_essay' => $soal->pertanyaan_essay
                    ];
                }),
                'soal_isian_singkat' => $lomba->soalIsianSingkat->map(function($soal) {
                    return [
                        'id' => $soal->id,
                        'nomor_soal' => $soal->nomor_soal,
                        'pertanyaan_isian' => $soal->pertanyaan_isian,
                        'jawaban_benar' => $soal->jawaban_benar
                    ];
                }),
                'stats' => [
                    'total_soal_pg' => $lomba->soal->count(),
                    'total_soal_essay' => $lomba->soalEssay->count(),
                    'total_soal_isian' => $lomba->soalIsianSingkat->count(),
                    'total_semua_soal' => $lomba->soal->count() + $lomba->soalEssay->count() + $lomba->soalIsianSingkat->count(),
                    'total_peserta' => $lomba->peserta->count()
                ]
            ];

            return response()->json([
                'success' => true,
                'message' => 'Detail lomba berhasil diambil',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil detail lomba',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 20. Daftar peserta (admin panel)
    public function getPeserta(Request $request)
    {
        try {
            $query = Peserta::with('cabangLomba');

            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama_lengkap', 'like', '%' . $search . '%')
                      ->orWhere('nomor_pendaftaran', 'like', '%' . $search . '%')
                      ->orWhere('username', 'like', '%' . $search . '%')
                      ->orWhere('asal_sekolah', 'like', '%' . $search . '%');
                });
            }

            // Filter by cabang lomba
            if ($request->has('cabang') && !empty($request->cabang)) {
                $query->whereHas('cabangLomba', function($q) use ($request) {
                    $q->where('nama_cabang', 'like', '%' . $request->cabang . '%');
                });
            }

            // Filter by status ujian
            if ($request->has('status') && !empty($request->status)) {
                $query->where('status_ujian', $request->status);
            }

            // Pagination
            $perPage = $request->get('per_page', 10);
            $peserta = $query->paginate($perPage);

            // Get statistics
            $stats = [
                'total_peserta' => Peserta::count(),
                'belum_mulai' => Peserta::where('status_ujian', 'belum_mulai')->count(),
                'sedang_ujian' => Peserta::where('status_ujian', 'sedang_ujian')->count(),
                'selesai' => Peserta::where('status_ujian', 'selesai')->count(),
                'per_cabang' => Peserta::with('cabangLomba')
                    ->get()
                    ->groupBy('cabangLomba.nama_cabang')
                    ->map->count()
            ];

            return response()->json([
                'success' => true,
                'message' => 'Daftar peserta berhasil diambil',
                'data' => $peserta->items(),
                'pagination' => [
                    'current_page' => $peserta->currentPage(),
                    'last_page' => $peserta->lastPage(),
                    'per_page' => $peserta->perPage(),
                    'total' => $peserta->total(),
                    'from' => $peserta->firstItem(),
                    'to' => $peserta->lastItem()
                ],
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data peserta',
                'error' => $e->getMessage()
            ], 500);
        }
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
