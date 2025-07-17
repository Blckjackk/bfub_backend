<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peserta;
use App\Models\Token;
use App\Models\CabangLomba;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // 1. Login peserta
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $peserta = Peserta::where('email', $request->email)->first();

        if (!$peserta || !Hash::check($request->password, $peserta->password_hash)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah'
            ], 401);
        }

        // Generate session token (simple implementation)
        $sessionToken = Str::random(60);
        
        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'peserta' => $peserta,
                'session_token' => $sessionToken
            ]
        ]);
    }

    // 2. Register peserta
    public function register(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'nomor_pendaftaran' => 'required|string|max:50|unique:peserta',
            'asal_sekolah' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:peserta',
            'password' => 'required|min:6',
            'cabang_lomba_id' => 'required|exists:cabang_lomba,id'
        ]);

        // Ambil data cabang lomba untuk waktu ujian
        $cabangLomba = CabangLomba::find($request->cabang_lomba_id);
        
        $peserta = Peserta::create([
            'nama_lengkap' => $request->nama_lengkap,
            'nomor_pendaftaran' => $request->nomor_pendaftaran,
            'asal_sekolah' => $request->asal_sekolah,
            'email' => $request->email,
            'password_hash' => Hash::make($request->password),
            'cabang_lomba_id' => $request->cabang_lomba_id,
            'status_ujian' => 'belum_mulai',
            'waktu_mulai' => now(), // Waktu registrasi sebagai placeholder
            'waktu_selesai' => now() // Akan diupdate ketika ujian selesai
        ]);

        // Otomatis generate 5 token
        $tokens = [];
        
        for ($i = 1; $i <= 5; $i++) {
            $token = Token::create([
                'kode_token' => strtoupper(substr($cabangLomba->nama_cabang, 0, 3)) . '-TOKEN-' . str_pad($peserta->id, 3, '0', STR_PAD_LEFT) . '-' . $i,
                'peserta_id' => $peserta->id,
                'cabang_lomba_id' => $request->cabang_lomba_id,
                'tipe' => $i == 1 ? 'utama' : 'cadangan',
                'status_token' => 'aktif',
                'created_at' => now(),
                'expired_at' => $cabangLomba->waktu_akhir_pengerjaan
            ]);
            $tokens[] = $token;
        }

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil. Anda mendapatkan 5 token.',
            'data' => [
                'peserta' => $peserta,
                'tokens' => $tokens
            ]
        ], 201);
    }

    // 3. Verifikasi token peserta
    public function verifyToken(Request $request)
    {
        $request->validate([
            'kode_token' => 'required|string'
        ]);

        $token = Token::where('kode_token', $request->kode_token)
                     ->where('status_token', 'aktif')
                     ->where('expired_at', '>', now())
                     ->first();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid atau sudah expired'
            ], 401);
        }

        // Update status token menjadi digunakan
        $token->update(['status_token' => 'digunakan']);

        return response()->json([
            'success' => true,
            'message' => 'Token valid',
            'data' => [
                'token' => $token,
                'peserta' => $token->peserta
            ]
        ]);
    }

    // 4. Request token ulang
    public function requestTokenUlang(Request $request)
    {
        $request->validate([
            'peserta_id' => 'required|exists:peserta,id'
        ]);

        $peserta = Peserta::find($request->peserta_id);
        
        // Cek apakah masih ada token yang aktif
        $activeTokens = Token::where('peserta_id', $request->peserta_id)
                            ->where('status_token', 'aktif')
                            ->where('expired_at', '>', now())
                            ->get();

        if ($activeTokens->count() > 0) {
            return response()->json([
                'success' => true,
                'message' => 'Anda masih memiliki token aktif',
                'data' => [
                    'active_tokens' => $activeTokens
                ]
            ]);
        }

        // Generate token baru
        $cabangLomba = $peserta->cabangLomba;
        $newToken = Token::create([
            'kode_token' => strtoupper(substr($cabangLomba->nama_cabang, 0, 3)) . '-ULANG-' . str_pad($peserta->id, 3, '0', STR_PAD_LEFT) . '-' . time(),
            'peserta_id' => $peserta->id,
            'cabang_lomba_id' => $peserta->cabang_lomba_id,
            'tipe' => 'cadangan',
            'status_token' => 'aktif',
            'created_at' => now(),
            'expired_at' => $cabangLomba->waktu_akhir_pengerjaan
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Token ulang berhasil dibuat',
            'data' => [
                'token' => $newToken
            ]
        ]);
    }
}
