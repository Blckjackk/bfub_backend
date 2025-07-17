<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SoalController;
use App\Http\Controllers\JawabanController;
use App\Http\Controllers\UjianController;
use App\Http\Controllers\AdminController;

Route::get('/api/', function () {
    return view('welcome');
});

// ================= AUTHENTICATION ROUTES =================
// 1. Login peserta
Route::post('/api/auth/login', [AuthController::class, 'login']);

// 2. Register peserta
Route::post('/api/auth/register', [AuthController::class, 'register']);

// 3. Verifikasi token peserta
Route::post('/api/auth/verify-token', [AuthController::class, 'verifyToken']);

// 4. Minta token ulang
Route::post('/api/auth/request-token-ulang', [AuthController::class, 'requestTokenUlang']);

// ================= PESERTA ROUTES =================
// 5. Data peserta saat ini (profil)
Route::get('/api/peserta/me', [PesertaController::class, 'me']);

// 6. Update data peserta
Route::put('/api/peserta/update', [PesertaController::class, 'update']);

// 7. Status ujian peserta
Route::get('/api/peserta/status-ujian', [PesertaController::class, 'statusUjian']);

// ================= SOAL ROUTES =================
// 8. Ambil semua soal pilihan ganda
Route::get('/api/soal/pg', [SoalController::class, 'getSoalPG']);

// 9. Ambil soal PG berdasarkan nomor
Route::get('/api/soal/pg/{nomor}', [SoalController::class, 'getSoalPGByNomor']);

// 16. Ambil soal essay
Route::get('/api/soal/essay', [SoalController::class, 'getSoalEssay']);

// ================= JAWABAN ROUTES =================
// 10. Kirim jawaban pilihan ganda
Route::post('/api/jawaban/pg', [JawabanController::class, 'submitJawabanPG']);

// 11. Ambil jawaban peserta (indikator terisi)
Route::get('/api/jawaban/pg', [JawabanController::class, 'getJawabanPG']);

// 17. Upload file tugas
Route::post('/api/jawaban/essay/upload', [JawabanController::class, 'uploadFileEssay']);

// 18. Preview file yang di-upload
Route::get('/api/jawaban/essay', [JawabanController::class, 'previewFileEssay']);

// 19. Re-upload file sebelum final
Route::put('/api/jawaban/essay/upload', [JawabanController::class, 'uploadFileEssay']);

// ================= UJIAN ROUTES =================
// 12. Peserta memulai ujian
Route::post('/api/ujian/mulai', [UjianController::class, 'mulaiUjian']);

// 13. Peserta submit ujian
Route::post('/api/ujian/selesai', [UjianController::class, 'selesaiUjian']);

// 14. Status waktu & pengerjaan
Route::get('/api/ujian/status', [UjianController::class, 'statusUjian']);

// 15. Auto save jawaban
Route::post('/api/ujian/auto-save', [UjianController::class, 'autoSave']);

// ================= ADMIN ROUTES =================
// 20. Daftar peserta (admin panel)
Route::get('/api/admin/peserta', [AdminController::class, 'getPeserta']);

// 22. Tambah soal pilihan ganda
Route::post('/api/admin/soal/pg', [AdminController::class, 'tambahSoalPG']);

// 23. Tambah soal essay
Route::post('/api/admin/soal/essay', [AdminController::class, 'tambahSoalEssay']);

// 24. Lihat semua jawaban peserta
Route::get('/api/admin/jawaban/peserta', [AdminController::class, 'getJawabanPeserta']);

// 25. Hitung nilai otomatis PG
Route::get('/api/admin/nilai/otomatis', [AdminController::class, 'hitungNilaiOtomatis']);

// 26. Export nilai peserta ke Excel
Route::get('/api/admin/export/excel', [AdminController::class, 'exportExcel']);

// 27. Download semua file upload
Route::get('/api/admin/export/files', [AdminController::class, 'downloadFiles']);

// ================= ADDITIONAL ROUTES =================
// 28. Endpoint simulasi ujian
Route::get('/api/simulasi/test', [UjianController::class, 'simulasiTest']);

// 29. Dokumentasi API
Route::get('/api/dokumentasi', function () {
    return response()->json([
        'success' => true,
        'message' => 'API Documentation',
        'data' => [
            'version' => '1.0.0',
            'endpoints' => 30,
            'documentation_url' => asset('API_DOCUMENTATION.md')
        ]
    ]);
});

// ================= EXISTING ROUTES =================
Route::get('/api/api/token-peserta', [TokenController::class, 'getTokenPeserta']);
Route::get('/api/api/token-peserta-by-name', [TokenController::class, 'getTokenByNamaPeserta']);


Route::get('/api/api/get-data-peserta', [PesertaController::class, 'getDataPeserta']);

Route::get('/api/api/test', function () {
    return response()->json(['message' => 'API works!']);
});


Route::get('/api/api/test-db', function () {
    try {
        $peserta = DB::table('peserta')->count();
        $token = DB::table('token')->count();
        $cabang = DB::table('cabang_lomba')->count();
        
        return response()->json([
            'success' => true,
            'message' => 'Database connection successful',
            'data' => [
                'peserta_count' => $peserta,
                'token_count' => $token,
                'cabang_lomba_count' => $cabang
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Database connection failed',
            'error' => $e->getMessage()
        ], 500);
    }
});
