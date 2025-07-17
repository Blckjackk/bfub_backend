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

// ================= JAWABAN ROUTES =================
Route::post('/jawaban/pg', [JawabanController::class, 'submitJawabanPG']);
Route::get('/jawaban/pg', [JawabanController::class, 'getJawabanPG']);
Route::post('/jawaban/essay/upload', [JawabanController::class, 'uploadFileEssay']);
Route::get('/jawaban/essay', [JawabanController::class, 'previewFileEssay']);
Route::put('/jawaban/essay/upload', [JawabanController::class, 'uploadFileEssay']);

// ================= UJIAN ROUTES =================
Route::post('/ujian/mulai', [UjianController::class, 'mulaiUjian']);
Route::post('/ujian/selesai', [UjianController::class, 'selesaiUjian']);
Route::get('/ujian/status', [UjianController::class, 'statusUjian']);
Route::post('/ujian/auto-save', [UjianController::class, 'autoSave']);

// ================= ADMIN ROUTES =================
Route::get('/admin/peserta', [AdminController::class, 'getPeserta']);
Route::post('/admin/soal/pg', [AdminController::class, 'tambahSoalPG']);
Route::post('/admin/soal/essay', [AdminController::class, 'tambahSoalEssay']);
Route::get('/admin/jawaban/peserta', [AdminController::class, 'getJawabanPeserta']);
Route::get('/admin/nilai/otomatis', [AdminController::class, 'hitungNilaiOtomatis']);
Route::get('/admin/export/excel', [AdminController::class, 'exportExcel']);
Route::get('/admin/export/files', [AdminController::class, 'downloadFiles']);

// ================= ADDITIONAL ROUTES =================
Route::get('/simulasi/test', [UjianController::class, 'simulasiTest']);
Route::get('/dokumentasi', function () {
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
Route::get('/api/token-peserta', [TokenController::class, 'getTokenPeserta']);
Route::get('/api/token-peserta-by-name', [TokenController::class, 'getTokenByNamaPeserta']);
Route::get('/api/get-data-peserta', [PesertaController::class, 'getDataPeserta']);
Route::get('/test', fn () => response()->json(['message' => 'API works!']));

