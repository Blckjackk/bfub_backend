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
                'soal_pg' => $lomba->soal->sortBy('nomor_soal')->values()->map(function($soal) {
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

            // Jawaban PG dengan detail soal
            $jawabanPG = $peserta->jawaban->map(function($jawaban) {
                return [
                    'nomor_soal' => $jawaban->soal->nomor_soal,
                    'pertanyaan' => $jawaban->soal->pertanyaan,
                    'pilihan_a' => $jawaban->soal->pilihan_a,
                    'pilihan_b' => $jawaban->soal->pilihan_b,
                    'pilihan_c' => $jawaban->soal->pilihan_c,
                    'pilihan_d' => $jawaban->soal->pilihan_d,
                    'jawaban_benar' => $jawaban->soal->jawaban_benar,
                    'jawaban_peserta' => $jawaban->jawaban_dipilih,
                    'benar' => $jawaban->benar,
                    'waktu_jawab' => $jawaban->waktu_jawab
                ];
            })->sortBy('nomor_soal');

            // Jawaban Essay dengan detail soal
            $jawabanEssay = $peserta->jawabanEssay->map(function($jawaban) {
                return [
                    'nomor_soal' => $jawaban->soalEssay->nomor_soal,
                    'pertanyaan' => $jawaban->soalEssay->pertanyaan_essay,
                    'jawaban_peserta' => $jawaban->jawaban_text,
                    'file_path' => $jawaban->file_path,
                    'file_name' => $jawaban->file_name,
                    'waktu_jawab' => $jawaban->waktu_jawab
                ];
            })->sortBy('nomor_soal');

            // Jawaban Isian Singkat dengan detail soal
            $jawabanIsianSingkat = $peserta->jawabanIsianSingkat->map(function($jawaban) {
                return [
                    'nomor_soal' => $jawaban->soalIsianSingkat->nomor_soal,
                    'pertanyaan' => $jawaban->soalIsianSingkat->pertanyaan_isian,
                    'jawaban_peserta' => $jawaban->jawaban_text,
                    'jawaban_benar' => $jawaban->soalIsianSingkat->jawaban_benar,
                    'benar' => $jawaban->benar ?? false,
                    'waktu_jawab' => $jawaban->waktu_jawab
                ];
            })->sortBy('nomor_soal');

            // Statistik
            $statistik = [
                'total_soal_pg' => $peserta->cabangLomba->soal->count(),
                'total_soal_essay' => $peserta->cabangLomba->soalEssay->count(),
                'total_soal_isian' => $peserta->cabangLomba->soalIsianSingkat->count(),
                'jawaban_pg_benar' => $peserta->jawaban->where('benar', true)->count(),
                'jawaban_pg_salah' => $peserta->jawaban->where('benar', false)->count(),
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
}
