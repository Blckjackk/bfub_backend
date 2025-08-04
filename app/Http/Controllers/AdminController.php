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
use App\Models\Token;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    // Get single PG question by ID
    public function getSoalPGById($id)
    {
        try {
            $soal = Soal::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Soal PG berhasil diambil',
                'data' => $soal
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Soal tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    // Get single Essay question by ID
    public function getSoalEssayById($id)
    {
        try {
            $soal = SoalEssay::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Soal Essay berhasil diambil',
                'data' => $soal
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Soal tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    // Get single Isian Singkat question by ID
    public function getSoalIsianSingkatById($id)
    {
        try {
            $soal = SoalIsianSingkat::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Soal Isian Singkat berhasil diambil',
                'data' => $soal
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Soal tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    // Update PG question by ID
    public function updateSoalPG(Request $request, $id)
    {
        try {
            $request->validate([
                'cabang_lomba_id' => 'required|exists:cabang_lomba,id',
                'pertanyaan' => 'required|string',
                'opsi_a' => 'required|string',
                'opsi_b' => 'required|string',
                'opsi_c' => 'required|string',
                'opsi_d' => 'required|string',
                'opsi_e' => 'required|string',
                'jawaban_benar' => 'required|string|in:A,B,C,D,E',
                'tipe_soal' => 'required|string|in:text,gambar'
            ]);

            $soal = Soal::findOrFail($id);
            
            // Update basic fields
            $soal->pertanyaan = $request->pertanyaan;
            $soal->opsi_a = $request->opsi_a;
            $soal->opsi_b = $request->opsi_b;
            $soal->opsi_c = $request->opsi_c;
            $soal->opsi_d = $request->opsi_d;
            $soal->opsi_e = $request->opsi_e;
            $soal->jawaban_benar = $request->jawaban_benar;
            $soal->tipe_soal = $request->tipe_soal;
            
            // Handle media uploads if present
            if ($request->hasFile('media_soal')) {
                if ($soal->media_soal) {
                    Storage::delete('public/' . $soal->media_soal);
                }
                $soal->media_soal = $request->file('media_soal')->store('soal_pg', 'public');
            }
            
            // Handle option media
            $mediaFields = ['opsi_a_media', 'opsi_b_media', 'opsi_c_media', 'opsi_d_media', 'opsi_e_media'];
            foreach ($mediaFields as $field) {
                if ($request->hasFile($field)) {
                    if ($soal->$field) {
                        Storage::delete('public/' . $soal->$field);
                    }
                    $soal->$field = $request->file($field)->store('soal_pg_options', 'public');
                }
            }

            $soal->save();

            return response()->json([
                'success' => true,
                'message' => 'Soal PG berhasil diupdate',
                'data' => $soal
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Soal tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate soal PG',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Update Essay question by ID
    public function updateSoalEssay(Request $request, $id)
    {
        try {
            $request->validate([
                'cabang_lomba_id' => 'required|exists:cabang_lomba,id',
                'pertanyaan_essay' => 'required|string'
            ]);

            $soal = SoalEssay::findOrFail($id);
            $soal->pertanyaan_essay = $request->pertanyaan_essay;
            $soal->save();

            return response()->json([
                'success' => true,
                'message' => 'Soal essay berhasil diupdate',
                'data' => $soal
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Soal tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate soal essay',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Update Isian Singkat question by ID
    public function updateSoalIsianSingkat(Request $request, $id)
    {
        try {
            $request->validate([
                'cabang_lomba_id' => 'required|exists:cabang_lomba,id',
                'pertanyaan_isian' => 'required|string',
                'jawaban_benar' => 'required|string'
            ]);

            $soal = SoalIsianSingkat::findOrFail($id);
            $soal->pertanyaan_isian = $request->pertanyaan_isian;
            $soal->jawaban_benar = $request->jawaban_benar;
            $soal->save();

            return response()->json([
                'success' => true,
                'message' => 'Soal isian singkat berhasil diupdate',
                'data' => $soal
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Soal tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate soal isian singkat',
                'error' => $e->getMessage()
            ], 500);
        }
    }

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
                'soal_pg' => $lomba->soal->sortBy('nomor_soal')->values()->map(function($soal) {
                    return [
                        'id' => $soal->id,
                        'nomor_soal' => $soal->nomor_soal,
                        'pertanyaan' => $soal->pertanyaan,
                        'media_soal' => $soal->media_soal,
                        'opsi_a' => $soal->opsi_a,
                        'opsi_a_media' => $soal->opsi_a_media,
                        'opsi_b' => $soal->opsi_b,
                        'opsi_b_media' => $soal->opsi_b_media,
                        'opsi_c' => $soal->opsi_c,
                        'opsi_c_media' => $soal->opsi_c_media,
                        'opsi_d' => $soal->opsi_d,
                        'opsi_d_media' => $soal->opsi_d_media,
                        'opsi_e' => $soal->opsi_e,
                        'opsi_e_media' => $soal->opsi_e_media,
                        'jawaban_benar' => $soal->jawaban_benar,
                        'tipe_soal' => $soal->tipe_soal,
                        'deskripsi_soal' => $soal->deskripsi_soal
                    ];
                }),
                'soal_essay' => $lomba->soalEssay->sortBy('nomor_soal')->values()->map(function($soal) {
                    return [
                        'id' => $soal->id,
                        'nomor_soal' => $soal->nomor_soal,
                        'pertanyaan_essay' => $soal->pertanyaan_essay
                    ];
                }),
                'soal_isian_singkat' => $lomba->soalIsianSingkat->sortBy('nomor_soal')->values()->map(function($soal) {
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

    // 86. Tambah soal PG dengan auto numbering
    public function tambahSoalPG(Request $request)
    {
        $request->validate([
            'cabang_lomba_id' => 'required|exists:cabang_lomba,id',
            'pertanyaan' => 'required|string',
            'opsi_a' => 'required|string',
            'opsi_b' => 'required|string',
            'opsi_c' => 'required|string',
            'opsi_d' => 'required|string',
            'opsi_e' => 'required|string',
            'jawaban_benar' => 'required|in:A,B,C,D,E',
            'tipe_soal' => 'string|in:text,gambar',
            'deskripsi_soal' => 'nullable|string'
        ]);

        // Auto generate nomor soal
        $nextNumber = Soal::where('cabang_lomba_id', $request->cabang_lomba_id)->max('nomor_soal') + 1;

        $soal = Soal::create([
            'cabang_lomba_id' => $request->cabang_lomba_id,
            'nomor_soal' => $nextNumber,
            'pertanyaan' => $request->pertanyaan,
            'opsi_a' => $request->opsi_a,
            'opsi_b' => $request->opsi_b,
            'opsi_c' => $request->opsi_c,
            'opsi_d' => $request->opsi_d,
            'opsi_e' => $request->opsi_e,
            'jawaban_benar' => $request->jawaban_benar,
            'tipe_soal' => $request->tipe_soal ?? 'text',
            'deskripsi_soal' => $request->deskripsi_soal
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Soal PG berhasil ditambahkan',
            'data' => $soal
        ]);
    }

        // 113. Tambah soal Essay dengan auto numbering
    public function tambahSoalEssay(Request $request)
    {
        $request->validate([
            'cabang_lomba_id' => 'required|exists:cabang_lomba,id',
            'pertanyaan_essay' => 'required|string'
        ]);

        // Auto generate nomor soal
        $nextNumber = SoalEssay::where('cabang_lomba_id', $request->cabang_lomba_id)->max('nomor_soal') + 1;

        $soal = SoalEssay::create([
            'cabang_lomba_id' => $request->cabang_lomba_id,
            'nomor_soal' => $nextNumber,
            'pertanyaan_essay' => $request->pertanyaan_essay
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Soal essay berhasil ditambahkan',
            'data' => $soal
        ]);
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

    // 28. Tambah soal isian singkat dengan auto numbering
    public function tambahSoalIsianSingkat(Request $request)
    {
        $request->validate([
            'cabang_lomba_id' => 'required|exists:cabang_lomba,id',
            'pertanyaan_isian' => 'required|string',
            'jawaban_benar' => 'required|string'
        ]);

        // Auto generate nomor soal
        $nextNumber = SoalIsianSingkat::where('cabang_lomba_id', $request->cabang_lomba_id)->max('nomor_soal') + 1;

        $soal = SoalIsianSingkat::create([
            'cabang_lomba_id' => $request->cabang_lomba_id,
            'nomor_soal' => $nextNumber,
            'pertanyaan_isian' => $request->pertanyaan_isian,
            'jawaban_benar' => $request->jawaban_benar
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Soal isian singkat berhasil ditambahkan',
            'data' => $soal
        ]);
    }

    // Delete soal PG dengan auto re-numbering
    public function deleteSoalPG($id)
    {
        try {
            $soal = Soal::findOrFail($id);
            $cabangLombaId = $soal->cabang_lomba_id;
            $deletedNomor = $soal->nomor_soal;

            // Delete soal
            $soal->delete();

            // Re-number soal yang nomornya lebih besar
            Soal::where('cabang_lomba_id', $cabangLombaId)
                ->where('nomor_soal', '>', $deletedNomor)
                ->decrement('nomor_soal');

            return response()->json([
                'success' => true,
                'message' => 'Soal PG berhasil dihapus dan nomor soal telah diperbarui'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus soal PG',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Delete soal Essay dengan auto re-numbering
    public function deleteSoalEssay($id)
    {
        try {
            $soal = SoalEssay::findOrFail($id);
            $cabangLombaId = $soal->cabang_lomba_id;
            $deletedNomor = $soal->nomor_soal;

            // Delete soal
            $soal->delete();

            // Re-number soal yang nomornya lebih besar
            SoalEssay::where('cabang_lomba_id', $cabangLombaId)
                ->where('nomor_soal', '>', $deletedNomor)
                ->decrement('nomor_soal');

            return response()->json([
                'success' => true,
                'message' => 'Soal Essay berhasil dihapus dan nomor soal telah diperbarui'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus soal Essay',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Delete soal Isian Singkat dengan auto re-numbering
    public function deleteSoalIsianSingkat($id)
    {
        try {
            $soal = SoalIsianSingkat::findOrFail($id);
            $cabangLombaId = $soal->cabang_lomba_id;
            $deletedNomor = $soal->nomor_soal;

            // Delete soal
            $soal->delete();

            // Re-number soal yang nomornya lebih besar
            SoalIsianSingkat::where('cabang_lomba_id', $cabangLombaId)
                ->where('nomor_soal', '>', $deletedNomor)
                ->decrement('nomor_soal');

            return response()->json([
                'success' => true,
                'message' => 'Soal Isian Singkat berhasil dihapus dan nomor soal telah diperbarui'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus soal Isian Singkat',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get hasil lomba - peserta yang sudah selesai ujian
    public function getHasilLomba(Request $request)
    {
        try {
            $query = Peserta::with(['cabangLomba', 'jawaban.soal', 'jawabanEssay.soalEssay', 'jawabanIsianSingkat.soalIsianSingkat'])
                ->where('status_ujian', 'selesai');

            // Filter by lomba if specified
            if ($request->has('lomba_id') && !empty($request->lomba_id)) {
                $query->where('cabang_lomba_id', $request->lomba_id);
            }

            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama_lengkap', 'like', '%' . $search . '%')
                      ->orWhere('nomor_pendaftaran', 'like', '%' . $search . '%');
                });
            }

            $peserta = $query->get();

            $hasilData = $peserta->map(function($peserta) {
                // Hitung statistik jawaban
                $jawabanPG = $peserta->jawaban;
                $jawabanBenar = $jawabanPG->where('benar', true)->count();
                $jawabanSalah = $jawabanPG->where('benar', false)->count();
                $totalSoalPG = $peserta->cabangLomba->soal->count();
                $totalSoalEssay = $peserta->cabangLomba->soalEssay->count();
                $totalSoalIsian = $peserta->cabangLomba->soalIsianSingkat->count();
                $totalSoal = $totalSoalPG + $totalSoalEssay + $totalSoalIsian;
                $soalTerjawab = $jawabanPG->count() + $peserta->jawabanEssay->count() + $peserta->jawabanIsianSingkat->count();

                return [
                    'id' => $peserta->id,
                    'noPendaftaran' => $peserta->nomor_pendaftaran,
                    'nama' => $peserta->nama_lengkap,
                    'cabor' => $peserta->cabangLomba->nama_cabang,
                    'mulai' => $peserta->waktu_mulai ? date('H:i', strtotime($peserta->waktu_mulai)) : '00:00',
                    'selesai' => $peserta->waktu_selesai ? date('H:i', strtotime($peserta->waktu_selesai)) : '00:00',
                    'jumlahSoal' => $totalSoal,
                    'soalTerjawab' => $soalTerjawab,
                    'soalBenar' => $jawabanBenar,
                    'soalSalah' => $jawabanSalah,
                    'nilai' => round($peserta->nilai_total ?? 0),
                    'asal_sekolah' => $peserta->asal_sekolah,
                    'waktu_pengerjaan' => $peserta->waktu_pengerjaan_total,
                    'isChecked' => false
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Data hasil lomba berhasil diambil',
                'data' => $hasilData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data hasil lomba',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get detail hasil lomba by peserta ID
    public function getHasilPeserta($id)
    {
        try {
            $peserta = Peserta::with([
                'cabangLomba.soal',
                'cabangLomba.soalEssay', 
                'cabangLomba.soalIsianSingkat',
                'jawaban.soal',
                'jawabanEssay.soalEssay',
                'jawabanIsianSingkat.soalIsianSingkat'
            ])->findOrFail($id);

            if ($peserta->status_ujian !== 'selesai') {
                return response()->json([
                    'success' => false,
                    'message' => 'Peserta belum menyelesaikan ujian'
                ], 400);
            }

            // Data peserta
            $detailPeserta = [
                'id' => $peserta->id,
                'nama_lengkap' => $peserta->nama_lengkap,
                'nomor_pendaftaran' => $peserta->nomor_pendaftaran,
                'asal_sekolah' => $peserta->asal_sekolah,
                'cabang_lomba' => $peserta->cabangLomba->nama_cabang,
                'waktu_mulai' => $peserta->waktu_mulai,
                'waktu_selesai' => $peserta->waktu_selesai,
                'waktu_pengerjaan_total' => $peserta->waktu_pengerjaan_total,
                'nilai_total' => $peserta->nilai_total,
                'status_ujian' => $peserta->status_ujian
            ];

            // Jawaban PG dengan detail soal - tampilkan semua soal dan cocokkan dengan jawaban
            $allSoalPG = $peserta->cabangLomba->soal->sortBy('nomor_soal');
            $jawabanPG = $allSoalPG->map(function($soal) use ($peserta) {
                $jawaban = $peserta->jawaban->where('soal_id', $soal->id)->first();
                
                return [
                    'nomor_soal' => $soal->nomor_soal,
                    'pertanyaan' => $soal->pertanyaan,
                    'pilihan_a' => $soal->pilihan_a,
                    'pilihan_b' => $soal->pilihan_b,
                    'pilihan_c' => $soal->pilihan_c,
                    'pilihan_d' => $soal->pilihan_d,
                    'jawaban_benar' => $soal->jawaban_benar,
                    'jawaban_peserta' => $jawaban ? $jawaban->jawaban_peserta : null, // Fix: gunakan jawaban_peserta
                    'benar' => $jawaban ? $jawaban->benar : false,
                    'waktu_jawab' => $jawaban ? $jawaban->waktu_dijawab : null // Fix: gunakan waktu_dijawab
                ];
            });

            // Jawaban Essay dengan detail soal - tampilkan semua soal dan cocokkan dengan jawaban
            $allSoalEssay = $peserta->cabangLomba->soalEssay->sortBy('nomor_soal');
            $jawabanEssay = $allSoalEssay->map(function($soal) use ($peserta) {
                $jawaban = $peserta->jawabanEssay->where('soal_essay_id', $soal->id)->first();
                
                return [
                    'nomor_soal' => $soal->nomor_soal,
                    'pertanyaan' => $soal->pertanyaan_essay,
                    'jawaban_peserta' => $jawaban ? $jawaban->jawaban_teks : null,
                    'file_path' => null, // Field tidak ada di tabel
                    'file_name' => null, // Field tidak ada di tabel  
                    'waktu_jawab' => null, // Field tidak ada di tabel
                    'score' => $jawaban ? $jawaban->score : 0, // Tambah field score
                    'jawaban_id' => $jawaban ? $jawaban->id : null // Tambah jawaban ID untuk update
                ];
            });

            // Jawaban Isian Singkat dengan detail soal - tampilkan semua soal dan cocokkan dengan jawaban
            $allSoalIsianSingkat = $peserta->cabangLomba->soalIsianSingkat->sortBy('nomor_soal');
            $jawabanIsianSingkat = $allSoalIsianSingkat->map(function($soal) use ($peserta) {
                $jawaban = $peserta->jawabanIsianSingkat->where('soal_isian_singkat_id', $soal->id)->first();
                
                return [
                    'nomor_soal' => $soal->nomor_soal,
                    'pertanyaan' => $soal->pertanyaan_isian,
                    'jawaban_peserta' => $jawaban ? $jawaban->jawaban_peserta : null, // Fix: gunakan jawaban_peserta
                    'jawaban_benar' => $soal->jawaban_benar,
                    'benar' => $jawaban ? ($jawaban->benar ?? false) : false,
                    'waktu_jawab' => $jawaban ? $jawaban->waktu_dijawab : null, // Fix: gunakan waktu_dijawab
                    'score' => $jawaban ? $jawaban->score : 0, // Tambah field score
                    'jawaban_id' => $jawaban ? $jawaban->id : null // Tambah jawaban ID untuk update
                ];
            });

            // Statistik
            $statistik = [
                'total_soal_pg' => $peserta->cabangLomba->soal->count(),
                'total_soal_essay' => $peserta->cabangLomba->soalEssay->count(),
                'total_soal_isian' => $peserta->cabangLomba->soalIsianSingkat->count(),
                'jawaban_pg_benar' => $peserta->jawaban->where('benar', true)->count(),
                'jawaban_pg_salah' => $peserta->jawaban->where('benar', false)->count(),
                'jawaban_pg_dijawab' => $peserta->jawaban->count(),
                'jawaban_essay_count' => $peserta->jawabanEssay->count(),
                'jawaban_isian_count' => $peserta->jawabanIsianSingkat->count(),
                'persentase_ketepatan' => $peserta->jawaban->count() > 0 
                    ? round(($peserta->jawaban->where('benar', true)->count() / $peserta->jawaban->count()) * 100, 2)
                    : 0
            ];

            return response()->json([
                'success' => true,
                'message' => 'Detail hasil peserta berhasil diambil',
                'data' => [
                    'peserta' => $detailPeserta,
                    'jawaban_pg' => $jawabanPG,
                    'jawaban_essay' => $jawabanEssay,
                    'jawaban_isian_singkat' => $jawabanIsianSingkat,
                    'statistik' => $statistik
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail hasil peserta',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get ranking peserta berdasarkan nilai
    public function getRanking(Request $request)
    {
        try {
            $query = Peserta::with('cabangLomba')
                ->where('status_ujian', 'selesai')
                ->whereNotNull('nilai_total')
                ->orderBy('nilai_total', 'desc');

            // Filter by lomba if specified
            if ($request->has('lomba_id') && !empty($request->lomba_id)) {
                $query->where('cabang_lomba_id', $request->lomba_id);
            }

            $peserta = $query->get();

            $ranking = $peserta->map(function($peserta, $index) {
                return [
                    'ranking' => $index + 1,
                    'id' => $peserta->id,
                    'nama_lengkap' => $peserta->nama_lengkap,
                    'nomor_pendaftaran' => $peserta->nomor_pendaftaran,
                    'asal_sekolah' => $peserta->asal_sekolah,
                    'cabang_lomba' => $peserta->cabangLomba->nama_cabang,
                    'nilai_total' => $peserta->nilai_total,
                    'waktu_pengerjaan' => $peserta->waktu_pengerjaan_total
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Ranking peserta berhasil diambil',
                'data' => $ranking
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil ranking peserta',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Delete hasil peserta (soft delete)
    public function deleteHasilPeserta($id)
    {
        try {
            $peserta = Peserta::findOrFail($id);
            
            // Reset status ujian dan nilai
            $peserta->update([
                'status_ujian' => 'belum_mulai',
                'nilai_total' => null,
                'waktu_mulai' => null,
                'waktu_selesai' => null,
                'waktu_pengerjaan_total' => null
            ]);

            // Hapus semua jawaban peserta
            $peserta->jawaban()->delete();
            $peserta->jawabanEssay()->delete();
            $peserta->jawabanIsianSingkat()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Hasil peserta berhasil dihapus dan direset'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus hasil peserta',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // =================== TOKEN MANAGEMENT METHODS ===================

    // Get all tokens with peserta and lomba info
    public function getAllTokens(Request $request)
    {
        try {
            $query = Token::with(['peserta', 'cabangLomba']);

            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->whereHas('peserta', function($q) use ($search) {
                    $q->where('nama_lengkap', 'like', '%' . $search . '%')
                      ->orWhere('nomor_pendaftaran', 'like', '%' . $search . '%');
                })->orWhere('kode_token', 'like', '%' . $search . '%');
            }

            // Filter by lomba
            if ($request->has('lomba_id') && !empty($request->lomba_id)) {
                $query->where('cabang_lomba_id', $request->lomba_id);
            }

            // Filter by status
            if ($request->has('status') && !empty($request->status)) {
                $query->where('status_token', $request->status);
            }

            $tokens = $query->orderBy('created_at', 'desc')->get();

            $tokenData = $tokens->map(function($token) {
                return [
                    'id' => $token->id,
                    'peserta' => $token->peserta ? $token->peserta->nama_lengkap : 'Belum Assigned',
                    'nomor_pendaftaran' => $token->peserta ? $token->peserta->nomor_pendaftaran : '-',
                    'kode_token' => $token->kode_token,
                    'cabor' => $token->cabangLomba ? $token->cabangLomba->nama_cabang : '-',
                    'tipe' => ucfirst($token->tipe),
                    'status' => ucfirst($token->status_token),
                    'created_at' => $token->created_at,
                    'expired_at' => $token->expired_at,
                    'peserta_id' => $token->peserta_id
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Data token berhasil diambil',
                'data' => $tokenData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get tokens by peserta ID
    public function getTokensByPeserta($peserta_id)
    {
        try {
            $tokens = Token::with(['cabangLomba'])
                ->where('peserta_id', $peserta_id)
                ->orderBy('tipe', 'desc') // utama first
                ->get();

            $tokenData = $tokens->map(function($token) {
                return [
                    'id' => $token->id,
                    'kode_token' => $token->kode_token,
                    'cabor' => $token->cabangLomba ? $token->cabangLomba->nama_cabang : '-',
                    'tipe' => $token->tipe,
                    'status' => $token->status_token,
                    'created_at' => $token->created_at,
                    'expired_at' => $token->expired_at,
                    'can_be_primary' => $token->status_token === 'aktif' // Only aktif tokens can be primary
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Token peserta berhasil diambil',
                'data' => $tokenData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil token peserta',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Set token as primary (utama)
    public function setTokenAsPrimary(Request $request, $token_id)
    {
        try {
            $token = Token::findOrFail($token_id);
            
            // Check if token is active
            if ($token->status_token !== 'aktif') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya token aktif yang bisa dijadikan utama'
                ], 400);
            }

            // Set all other tokens of this peserta to 'cadangan'
            Token::where('peserta_id', $token->peserta_id)
                ->where('id', '!=', $token_id)
                ->update(['tipe' => 'cadangan']);

            // Set this token as 'utama'
            $token->update(['tipe' => 'utama']);

            return response()->json([
                'success' => true,
                'message' => 'Token berhasil dijadikan sebagai token utama'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengatur token utama',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Generate new tokens for peserta
    public function generateTokens(Request $request)
    {
        $request->validate([
            'peserta_id' => 'required|exists:peserta,id',
            'cabang_lomba_id' => 'required|exists:cabang_lomba,id',
            'jumlah_token' => 'required|integer|min:1|max:5'
        ]);

        try {
            $peserta = Peserta::findOrFail($request->peserta_id);
            $cabangLomba = CabangLomba::findOrFail($request->cabang_lomba_id);
            
            $tokens = [];
            
            for ($i = 0; $i < $request->jumlah_token; $i++) {
                // Generate unique token code
                $kodeToken = strtoupper($cabangLomba->nama_cabang) . '-' . 
                           strtoupper($peserta->nomor_pendaftaran) . '-' . 
                           sprintf('%03d', $i + 1);

                // Check if this is the first token (will be primary)
                $tipe = $i === 0 ? 'utama' : 'cadangan';
                
                $token = Token::create([
                    'kode_token' => $kodeToken,
                    'peserta_id' => $request->peserta_id,
                    'cabang_lomba_id' => $request->cabang_lomba_id,
                    'tipe' => $tipe,
                    'status_token' => 'aktif',
                    'created_at' => now(),
                    'expired_at' => now()->addDays(30) // Token expires in 30 days
                ]);

                $tokens[] = $token;
            }

            return response()->json([
                'success' => true,
                'message' => $request->jumlah_token . ' token berhasil dibuat',
                'data' => $tokens
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Delete token
    public function deleteToken($id)
    {
        try {
            $token = Token::findOrFail($id);
            
            // Check if token is being used
            if ($token->status_token === 'digunakan') {
                return response()->json([
                    'success' => false,
                    'message' => 'Token yang sedang digunakan tidak bisa dihapus'
                ], 400);
            }

            $token->delete();

            return response()->json([
                'success' => true,
                'message' => 'Token berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Mark tokens as expired (hangus)
    public function markTokensAsExpired(Request $request)
    {
        $request->validate([
            'token_ids' => 'required|array',
            'token_ids.*' => 'exists:token,id'
        ]);

        try {
            Token::whereIn('id', $request->token_ids)
                ->where('status_token', '!=', 'digunakan') // Don't expire tokens in use
                ->update(['status_token' => 'hangus']);

            return response()->json([
                'success' => true,
                'message' => 'Token berhasil ditandai sebagai hangus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menandai token sebagai hangus',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get tokens grouped by peserta
    public function getTokensGroupedByPeserta(Request $request)
    {
        try {
            $query = Token::with(['peserta', 'cabangLomba']);

            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->whereHas('peserta', function($q) use ($search) {
                    $q->where('nama_lengkap', 'like', '%' . $search . '%')
                      ->orWhere('nomor_pendaftaran', 'like', '%' . $search . '%');
                });
            }

            // Filter by lomba
            if ($request->has('lomba_id') && !empty($request->lomba_id)) {
                $query->where('cabang_lomba_id', $request->lomba_id);
            }

            // Filter by status
            if ($request->has('status') && !empty($request->status)) {
                $query->where('status_token', $request->status);
            }

            $tokens = $query->orderBy('created_at', 'desc')->get();

            // Group tokens by peserta
            $groupedTokens = $tokens->groupBy('peserta_id')->map(function($pesertaTokens) {
                $firstToken = $pesertaTokens->first();
                $utamaToken = $pesertaTokens->where('tipe', 'utama')->first();
                $cadanganTokens = $pesertaTokens->where('tipe', 'cadangan');
                
                return [
                    'peserta_id' => $firstToken->peserta_id,
                    'peserta' => $firstToken->peserta ? $firstToken->peserta->nama_lengkap : 'Belum Assigned',
                    'nomor_pendaftaran' => $firstToken->peserta ? $firstToken->peserta->nomor_pendaftaran : '-',
                    'cabor' => $firstToken->cabangLomba ? $firstToken->cabangLomba->nama_cabang : '-',
                    'cabang_lomba_id' => $firstToken->cabang_lomba_id,
                    'token_utama' => $utamaToken ? [
                        'id' => $utamaToken->id,
                        'kode_token' => $utamaToken->kode_token,
                        'status' => ucfirst($utamaToken->status_token),
                        'created_at' => $utamaToken->created_at,
                        'expired_at' => $utamaToken->expired_at,
                    ] : null,
                    'token_cadangan' => $cadanganTokens->map(function($token) {
                        return [
                            'id' => $token->id,
                            'kode_token' => $token->kode_token,
                            'status' => ucfirst($token->status_token),
                            'created_at' => $token->created_at,
                            'expired_at' => $token->expired_at,
                        ];
                    })->values(),
                    'total_tokens' => $pesertaTokens->count(),
                    'aktif_tokens' => $pesertaTokens->where('status_token', 'aktif')->count(),
                    'digunakan_tokens' => $pesertaTokens->where('status_token', 'digunakan')->count(),
                    'hangus_tokens' => $pesertaTokens->where('status_token', 'hangus')->count(),
                ];
            })->values();

            return response()->json([
                'success' => true,
                'message' => 'Data token berhasil diambil',
                'data' => $groupedTokens
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Update token status
    public function updateTokenStatus(Request $request, $token_id)
    {
        $request->validate([
            'status' => 'required|in:aktif,digunakan,hangus'
        ]);

        try {
            $token = Token::findOrFail($token_id);
            
            $token->update([
                'status_token' => $request->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status token berhasil diubah',
                'data' => [
                    'id' => $token->id,
                    'kode_token' => $token->kode_token,
                    'status' => ucfirst($token->status_token)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update nilai essay
     */
    public function updateNilaiEssay(Request $request, $jawabanId)
    {
        try {
            $request->validate([
                'nilai' => 'required|numeric|min:0'
            ]);

            $jawabanEssay = JawabanEssay::find($jawabanId);
            
            if (!$jawabanEssay) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jawaban essay tidak ditemukan'
                ], 404);
            }

            $jawabanEssay->score = $request->nilai;
            $jawabanEssay->save();

            return response()->json([
                'success' => true,
                'message' => 'Nilai essay berhasil diupdate',
                'data' => [
                    'id' => $jawabanEssay->id,
                    'score' => $jawabanEssay->score
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate nilai essay',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update nilai isian singkat
     */
    public function updateNilaiIsianSingkat(Request $request, $jawabanId)
    {
        try {
            $request->validate([
                'nilai' => 'required|numeric|min:0'
            ]);

            $jawabanIsianSingkat = JawabanIsianSingkat::find($jawabanId);
            
            if (!$jawabanIsianSingkat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jawaban isian singkat tidak ditemukan'
                ], 404);
            }

            $jawabanIsianSingkat->score = $request->nilai;
            $jawabanIsianSingkat->save();

            return response()->json([
                'success' => true,
                'message' => 'Nilai isian singkat berhasil diupdate',
                'data' => [
                    'id' => $jawabanIsianSingkat->id,
                    'score' => $jawabanIsianSingkat->score
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate nilai isian singkat',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats()
    {
        try {
            // Hitung total peserta
            $totalPeserta = Peserta::count();
            
            // Hitung total lomba
            $totalLomba = CabangLomba::count();
            
            // Hitung peserta yang sedang mengerjakan ujian (status: mengerjakan)
            $pesertaOnline = Peserta::where('status_ujian', 'mengerjakan')->count();
            
            // Statistik tambahan
            $pesertaSelesai = Peserta::where('status_ujian', 'selesai')->count();
            $pesertaBelumMulai = Peserta::where('status_ujian', 'belum_mulai')->count();
            
            // Token statistics
            $tokenAktif = Token::where('status_token', 'aktif')->count();
            $tokenTerpakai = Token::where('status_token', 'terpakai')->count();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total_peserta' => $totalPeserta,
                    'total_lomba' => $totalLomba,
                    'peserta_online' => $pesertaOnline, // Peserta yang sedang mengerjakan
                    'peserta_selesai' => $pesertaSelesai,
                    'peserta_belum_mulai' => $pesertaBelumMulai,
                    'token_aktif' => $tokenAktif,
                    'token_terpakai' => $tokenTerpakai
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik dashboard',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete peserta
     */
    public function deletePeserta($id)
    {
        try {
            $peserta = Peserta::find($id);
            
            if (!$peserta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Peserta tidak ditemukan'
                ], 404);
            }

            // Delete related data in proper order to handle foreign key constraints
            Token::where('peserta_id', $id)->delete();
            Jawaban::where('peserta_id', $id)->delete();
            JawabanEssay::where('peserta_id', $id)->delete();
            JawabanIsianSingkat::where('peserta_id', $id)->delete();

            // Delete peserta
            $peserta->delete();

            return response()->json([
                'success' => true,
                'message' => 'Peserta berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus peserta',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete multiple peserta
     */
    public function deleteBatchPeserta(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            
            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada peserta yang dipilih'
                ], 400);
            }

            // Delete related data for all peserta in proper order to handle foreign key constraints
            Token::whereIn('peserta_id', $ids)->delete();
            Jawaban::whereIn('peserta_id', $ids)->delete();
            JawabanEssay::whereIn('peserta_id', $ids)->delete();
            JawabanIsianSingkat::whereIn('peserta_id', $ids)->delete();

            // Delete peserta
            $deleted = Peserta::whereIn('id', $ids)->delete();

            return response()->json([
                'success' => true,
                'message' => "Berhasil menghapus {$deleted} peserta"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus peserta',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new cabang lomba
     */
    public function createLomba(Request $request)
    {
        try {
            // Log the incoming request for debugging
            Log::info('Create Lomba Request:', $request->all());

            $request->validate([
                'nama_cabang' => 'required|string|max:100',
                'deskripsi_lomba' => 'nullable|string',
                'waktu_mulai_pengerjaan' => 'required|string', // Accept as string first, then convert
                'waktu_akhir_pengerjaan' => 'required|string'   // Accept as string first, then convert
            ]);

            // Convert datetime strings to proper format
            $waktuMulai = \Carbon\Carbon::parse($request->waktu_mulai_pengerjaan);
            $waktuAkhir = \Carbon\Carbon::parse($request->waktu_akhir_pengerjaan);

            // Validate that end time is after start time
            if ($waktuAkhir <= $waktuMulai) {
                return response()->json([
                    'success' => false,
                    'message' => 'Waktu akhir pengerjaan harus setelah waktu mulai'
                ], 422);
            }

            $lomba = CabangLomba::create([
                'nama_cabang' => $request->nama_cabang,
                'deskripsi_lomba' => $request->deskripsi_lomba,
                'waktu_mulai_pengerjaan' => $waktuMulai,
                'waktu_akhir_pengerjaan' => $waktuAkhir
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lomba berhasil dibuat',
                'data' => $lomba
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Create Lomba Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat lomba: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete lomba
     */
    public function deleteLomba($id)
    {
        try {
            $lomba = CabangLomba::find($id);
            
            if (!$lomba) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lomba tidak ditemukan'
                ], 404);
            }

            // Check if there are peserta using this lomba
            $pesertaCount = Peserta::where('cabang_lomba_id', $id)->count();
            if ($pesertaCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Tidak dapat menghapus lomba karena masih ada {$pesertaCount} peserta yang terdaftar"
                ], 400);
            }

            // Delete related soal
            Soal::where('cabang_lomba_id', $id)->delete();
            SoalEssay::where('cabang_lomba_id', $id)->delete();
            SoalIsianSingkat::where('cabang_lomba_id', $id)->delete();

            // Delete lomba
            $lomba->delete();

            return response()->json([
                'success' => true,
                'message' => 'Lomba berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus lomba',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create soal PG
     */
    public function createSoalPG(Request $request)
    {
        try {
            // Base validation rules
            $rules = [
                'cabang_lomba_id' => 'required|exists:cabang_lomba,id',
                'tipe_soal' => 'required|in:text,gambar',
                'pertanyaan' => 'nullable|string',
                'jawaban_benar' => 'required|in:A,B,C,D,E',
                'media_soal' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'opsi_a_media' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'opsi_b_media' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'opsi_c_media' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'opsi_d_media' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'opsi_e_media' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ];

            // Conditional validation based on tipe_soal
            if ($request->tipe_soal === 'text') {
                // For text type, text options are required
                $rules['opsi_a'] = 'required|string';
                $rules['opsi_b'] = 'required|string';
                $rules['opsi_c'] = 'required|string';
                $rules['opsi_d'] = 'required|string';
                $rules['opsi_e'] = 'required|string';
            } else {
                // For gambar type, text options are optional
                $rules['opsi_a'] = 'nullable|string';
                $rules['opsi_b'] = 'nullable|string';
                $rules['opsi_c'] = 'nullable|string';
                $rules['opsi_d'] = 'nullable|string';
                $rules['opsi_e'] = 'nullable|string';
            }

            $request->validate($rules);

            // Get next nomor_soal
            $nextNomor = Soal::where('cabang_lomba_id', $request->cabang_lomba_id)->max('nomor_soal') + 1;

            $data = [
                'cabang_lomba_id' => $request->cabang_lomba_id,
                'nomor_soal' => $nextNomor,
                'tipe_soal' => $request->tipe_soal,
                'pertanyaan' => $request->pertanyaan ?? '',
                'opsi_a' => $request->opsi_a ?? '',
                'opsi_b' => $request->opsi_b ?? '',
                'opsi_c' => $request->opsi_c ?? '',
                'opsi_d' => $request->opsi_d ?? '',
                'opsi_e' => $request->opsi_e ?? '',
                'jawaban_benar' => $request->jawaban_benar,
            ];

            // Handle file uploads
            if ($request->hasFile('media_soal')) {
                $file = $request->file('media_soal');
                $filename = time() . '_soal_' . str_replace(' ', '_', $file->getClientOriginalName());
                
                // Ensure directory exists
                $uploadPath = public_path('storage/soal');
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                $file->move($uploadPath, $filename);
                $data['media_soal'] = 'storage/soal/' . $filename;
            }

            // Handle option media uploads
            $options = ['a', 'b', 'c', 'd', 'e'];
            foreach ($options as $option) {
                $fieldName = "opsi_{$option}_media";
                if ($request->hasFile($fieldName)) {
                    $file = $request->file($fieldName);
                    $filename = time() . "_opsi_{$option}_" . str_replace(' ', '_', $file->getClientOriginalName());
                    
                    // Ensure directory exists
                    $uploadPath = public_path('storage/soal');
                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }
                    
                    $file->move($uploadPath, $filename);
                    $data[$fieldName] = 'storage/soal/' . $filename;
                }
            }

            $soal = Soal::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Soal PG berhasil dibuat',
                'data' => $soal
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat soal PG',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Create soal Essay
     */
    public function createSoalEssay(Request $request)
    {
        try {
            $request->validate([
                'cabang_lomba_id' => 'required|exists:cabang_lomba,id',
                'pertanyaan_essay' => 'required|string',
            ]);

            // Get next nomor_soal
            $nextNomor = SoalEssay::where('cabang_lomba_id', $request->cabang_lomba_id)->max('nomor_soal') + 1;

            $soal = SoalEssay::create([
                'cabang_lomba_id' => $request->cabang_lomba_id,
                'nomor_soal' => $nextNomor,
                'pertanyaan_essay' => $request->pertanyaan_essay,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Soal Essay berhasil dibuat',
                'data' => $soal
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat soal Essay',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Create soal Isian Singkat
     */
    public function createSoalIsianSingkat(Request $request)
    {
        try {
            $request->validate([
                'cabang_lomba_id' => 'required|exists:cabang_lomba,id',
                'pertanyaan_isian' => 'required|string',
                'jawaban_benar' => 'required|string',
            ]);

            // Get next nomor_soal
            $nextNomor = SoalIsianSingkat::where('cabang_lomba_id', $request->cabang_lomba_id)->max('nomor_soal') + 1;

            $soal = SoalIsianSingkat::create([
                'cabang_lomba_id' => $request->cabang_lomba_id,
                'nomor_soal' => $nextNomor,
                'pertanyaan_isian' => $request->pertanyaan_isian,
                'jawaban_benar' => $request->jawaban_benar,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Soal Isian Singkat berhasil dibuat',
                'data' => $soal
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat soal Isian Singkat',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Debug endpoint to test file uploads
     */
    public function debugUpload(Request $request)
    {
        try {
            $data = [
                'has_files' => $request->hasFile('media_soal'),
                'all_files' => array_keys($request->allFiles()),
                'all_input' => $request->all(),
                'storage_path' => public_path('storage/soal'),
                'storage_exists' => is_dir(public_path('storage/soal')),
                'storage_writable' => is_writable(public_path('storage/soal'))
            ];

            return response()->json([
                'success' => true,
                'debug_data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
