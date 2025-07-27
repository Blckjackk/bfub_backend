<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\SoalController;
use App\Http\Controllers\JawabanController;
use App\Http\Controllers\UjianController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\IsianSingkatController;

Route::get('/ping', function () {
    return response()->json(['message' => 'API routes are working!']);
});

// ================= AUTHENTICATION ROUTES =================
// Route::post('/auth/login', [AuthController::class, 'login']);
// Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/register', [AuthController::class, 'register']); // Added direct register route
// Route::get('/register', [AuthController::class, 'register']);  // Support GET method too
Route::post('/auth/verify-token', [AuthController::class, 'verifyToken']);
Route::post('/auth/request-token-ulang', [AuthController::class, 'requestTokenUlang']);

// ================= PESERTA ROUTES =================
Route::get('/peserta/me', [PesertaController::class, 'me']);
Route::put('/peserta/update', [PesertaController::class, 'update']);
Route::get('/peserta/status-ujian', [PesertaController::class, 'statusUjian']);

// ================= SOAL ROUTES =================
Route::get('/soal/pg', [SoalController::class, 'getSoalPG']);
Route::get('/soal/pg/{nomor}', [SoalController::class, 'getSoalPGByNomor']);
Route::get('/soal/essay', [SoalController::class, 'getSoalEssay']);
Route::get('/soal/isian-singkat', [IsianSingkatController::class, 'getSoalIsianSingkat']);
Route::get('/soal/isian-singkat/{nomor}', [IsianSingkatController::class, 'getSoalIsianSingkatByNomor']);

// ================= JAWABAN ROUTES =================
Route::post('/jawaban/pg', [JawabanController::class, 'submitJawabanPG']);
Route::get('/jawaban/pg', [JawabanController::class, 'getJawabanPG']);
Route::post('/jawaban/essay/upload', [JawabanController::class, 'uploadFileEssay']);
Route::get('/jawaban/essay', [JawabanController::class, 'previewFileEssay']);
Route::put('/jawaban/essay/upload', [JawabanController::class, 'uploadFileEssay']);
Route::post('/jawaban/isian-singkat', [IsianSingkatController::class, 'submitJawabanIsianSingkat']);
Route::get('/jawaban/isian-singkat', [IsianSingkatController::class, 'getJawabanIsianSingkat']);
Route::get('/jawaban/isian-singkat/preview', [IsianSingkatController::class, 'previewJawabanIsianSingkat']);

// ================= UJIAN ROUTES =================
Route::post('/ujian/mulai', [UjianController::class, 'mulaiUjian']);
Route::post('/ujian/selesai', [UjianController::class, 'selesaiUjian']);
Route::get('/ujian/status', [UjianController::class, 'statusUjian']);
Route::post('/ujian/auto-save', [UjianController::class, 'autoSave']);

// ================= ADMIN ROUTES =================
Route::get('/admin/peserta', [AdminController::class, 'getPeserta']);
Route::post('/admin/soal/pg', [AdminController::class, 'tambahSoalPG']);
Route::post('/admin/soal/essay', [AdminController::class, 'tambahSoalEssay']);
Route::post('/admin/soal/isian-singkat', [AdminController::class, 'tambahSoalIsianSingkat']);
Route::get('/admin/jawaban/peserta', [AdminController::class, 'getJawabanPeserta']);
Route::get('/admin/nilai/otomatis', [AdminController::class, 'hitungNilaiOtomatis']);
Route::get('/admin/export/excel', [AdminController::class, 'exportExcel']);
Route::get('/admin/export/files', [AdminController::class, 'downloadFiles']);

// ================= AUTHENTICATION MISSING ROUTES =================
Route::post('/login', [AuthController::class, 'login']); // Missing login endpoint

// ================= LOMBA MANAGEMENT ROUTES (MISSING) =================
Route::get('/lomba', [AdminController::class, 'getLomba']); // Get all lomba
Route::post('/lomba', [AdminController::class, 'createLomba']); // Create lomba
Route::get('/lomba/{id}', [AdminController::class, 'getLombaById']); // Get lomba by ID
Route::put('/lomba/{id}', [AdminController::class, 'updateLomba']); // Update lomba
Route::delete('/lomba/{id}', [AdminController::class, 'deleteLomba']); // Delete lomba

// ================= KATEGORI MANAGEMENT ROUTES (MISSING) =================
Route::get('/kategori', [AdminController::class, 'getKategori']); // Get all kategori
Route::post('/kategori', [AdminController::class, 'createKategori']); // Create kategori
Route::put('/kategori/{id}', [AdminController::class, 'updateKategori']); // Update kategori
Route::delete('/kategori/{id}', [AdminController::class, 'deleteKategori']); // Delete kategori

// ================= PESERTA MANAGEMENT ROUTES (MISSING) =================
Route::get('/admin/peserta/{id}', [AdminController::class, 'getPesertaById']); // Get peserta by ID
Route::post('/admin/peserta', [AdminController::class, 'createPeserta']); // Create peserta
Route::put('/admin/peserta/{id}', [AdminController::class, 'updatePesertaById']); // Update peserta
Route::delete('/admin/peserta/{id}', [AdminController::class, 'deletePeserta']); // Delete peserta

// ================= TOKEN MANAGEMENT ROUTES (MISSING) =================
Route::get('/admin/token', [TokenController::class, 'getAllTokens']); // Get all tokens
Route::post('/admin/token/generate', [TokenController::class, 'generateTokens']); // Generate tokens
Route::delete('/admin/token/{id}', [TokenController::class, 'deleteToken']); // Delete token

// ================= SOAL MANAGEMENT ROUTES (MISSING) =================
Route::get('/admin/soal/pg', [AdminController::class, 'getSoalPG']); // Get all soal PG
Route::get('/admin/soal/essay', [AdminController::class, 'getSoalEssay']); // Get all soal essay
Route::get('/admin/soal/isian-singkat', [AdminController::class, 'getSoalIsianSingkat']); // Get all soal isian singkat
Route::put('/admin/soal/pg/{id}', [AdminController::class, 'updateSoalPG']); // Update soal PG
Route::put('/admin/soal/essay/{id}', [AdminController::class, 'updateSoalEssay']); // Update soal essay
Route::put('/admin/soal/isian-singkat/{id}', [AdminController::class, 'updateSoalIsianSingkat']); // Update soal isian singkat
Route::delete('/admin/soal/pg/{id}', [AdminController::class, 'deleteSoalPG']); // Delete soal PG
Route::delete('/admin/soal/essay/{id}', [AdminController::class, 'deleteSoalEssay']); // Delete soal essay
Route::delete('/admin/soal/isian-singkat/{id}', [AdminController::class, 'deleteSoalIsianSingkat']); // Delete soal isian singkat

// ================= HASIL & NILAI ROUTES =================
Route::get('/admin/hasil/lomba', [AdminController::class, 'getHasilLomba']); // Get hasil lomba 
Route::get('/admin/hasil/peserta/{id}', [AdminController::class, 'getHasilPeserta']); // Get hasil by peserta ID
Route::delete('/admin/hasil/peserta/{id}', [AdminController::class, 'deleteHasilPeserta']); // Delete hasil peserta
Route::get('/peserta/hasil', [PesertaController::class, 'getHasilLomba']); // Get hasil lomba peserta
Route::get('/admin/hasil/lomba/{id}', [AdminController::class, 'getHasilLomba']); // Get hasil by lomba ID (with filter)
Route::get('/admin/ranking', [AdminController::class, 'getRanking']); // Get ranking peserta

// ================= FILE IMPORT ROUTES (MISSING) =================
Route::post('/admin/import/peserta', [AdminController::class, 'importPeserta']); // Import peserta from file
Route::post('/admin/import/soal', [AdminController::class, 'importSoal']); // Import soal from file

// ================= CBT SYSTEM ROUTES (MISSING) =================
Route::get('/durasi', [UjianController::class, 'getDurasi']); // Get durasi ujian
Route::post('/peserta/cek-token', [AuthController::class, 'cekToken']); // Cek token peserta
Route::patch('/token-hangus', [TokenController::class, 'tokenHangus']); // Mark token as hangus

// ================= LANDING PAGE ROUTES (MISSING) =================
Route::get('/jenis-perlombaan', [AdminController::class, 'getJenisPerlombaan']); // Get jenis perlombaan for landing page
Route::get('/pendaftaran-aktif', [AdminController::class, 'getPendaftaranAktif']); // Get periode lomba aktif

// ================= DASHBOARD PESERTA ROUTES (MISSING) =================
Route::get('/deskripsi-lomba/{id}', [AdminController::class, 'getDeskripsiLomba']); // Get deskripsi lomba
Route::get('/me', [PesertaController::class, 'me']); // Alias for peserta/me
Route::get('/peserta/ambil-token', [TokenController::class, 'ambilToken']); // Ambil token peserta

// ================= DASHBOARD ADMIN STATS ROUTES (MISSING) =================
Route::get('/stats/lomba', [AdminController::class, 'getStatsLomba']); // Get stats lomba
Route::get('/stats/peserta', [AdminController::class, 'getStatsPeserta']); // Get stats peserta  
Route::get('/stats/pendaftaran', [AdminController::class, 'getStatsPendaftaran']); // Get stats pendaftaran
Route::get('/stats/nilai', [AdminController::class, 'getStatsNilai']); // Get stats nilai

// ================= CBT JAWABAN ESSAY ROUTES (MISSING) =================
Route::get('/soal/esai/{id}', [SoalController::class, 'getSoalEssayById']); // Get soal essay by ID
Route::post('/jawaban/esai/{id}', [JawabanController::class, 'submitJawabanEssay']); // Submit jawaban essay

// ================= KONFIRMASI JAWABAN ROUTES (MISSING) =================
Route::get('/status-jawaban', [JawabanController::class, 'getStatusJawaban']); // Get status jawaban peserta

// ================= NILAI MANAGEMENT ROUTES (MISSING) =================
Route::get('/nilai', [AdminController::class, 'getNilai']); // Get all nilai
Route::put('/nilai/{id}', [AdminController::class, 'updateNilai']); // Update nilai
Route::get('/pendaftaran/lomba/{id}', [AdminController::class, 'getPendaftaranByLomba']); // Get pendaftaran by lomba

// ================= ADDITIONAL ROUTES =================
Route::get('/simulasi/test', [UjianController::class, 'simulasiTest']);
Route::get('/dokumentasi', function () {
    return response()->json([
        'success' => true,
        'message' => 'API Documentation',
        'data' => [
            'version' => '1.0.0',
            'endpoints' => 50,
            'documentation_url' => asset('API_DOCUMENTATION.md')
        ]
    ]);
});

// ================= EXISTING ROUTES =================
Route::get('/api/token-peserta', [TokenController::class, 'getTokenPeserta']);
Route::get('/api/token-peserta-by-name', [TokenController::class, 'getTokenByNamaPeserta']);
Route::get('/api/get-data-peserta', [PesertaController::class, 'getDataPeserta']);
Route::get('/test', fn () => response()->json(['message' => 'API works!']));

