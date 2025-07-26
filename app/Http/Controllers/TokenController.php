<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Token;
use App\Models\Peserta;

class TokenController extends Controller
{

    public function getTokenPeserta(Request $request)
    {
        try {
            // Ambil semua peserta dengan token-tokennya
            $pesertaWithTokens = Peserta::with(['token', 'cabangLomba'])
                ->get()
                ->map(function ($peserta) {
                    return [
                        'peserta_id' => $peserta->id,
                        'nama_peserta' => $peserta->nama_lengkap,
                        'nomor_pendaftaran' => $peserta->nomor_pendaftaran,
                        'asal_sekolah' => $peserta->asal_sekolah,
                        'email' => $peserta->email,
                        'cabang_lomba' => [
                            'id' => $peserta->cabangLomba->id ?? null,
                            'nama_cabang' => $peserta->cabangLomba->nama_cabang ?? null,
                        ],
                        'jumlah_token' => $peserta->token->count(),
                        'tokens' => $peserta->token->map(function ($token) {
                            return [
                                'id' => $token->id,
                                'kode_token' => $token->kode_token,
                                'tipe' => $token->tipe,
                                'status_token' => $token->status_token,
                                'created_at' => $token->created_at,
                                'expired_at' => $token->expired_at,
                            ];
                        })
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Data token peserta berhasil diambil',
                'data' => $pesertaWithTokens
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data token peserta',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Method tambahan untuk mendapatkan token berdasarkan nama peserta
    public function getTokenByNamaPeserta(Request $request)
    {
        try {
            $namaPeserta = $request->input('nama_peserta');
            
            if (!$namaPeserta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nama peserta harus diisi'
                ], 400);
            }

            // Cari peserta berdasarkan nama (case insensitive)
            $peserta = Peserta::with(['token', 'cabangLomba'])
                ->whereRaw('LOWER(nama_lengkap) LIKE ?', ['%' . strtolower($namaPeserta) . '%'])
                ->get()
                ->map(function ($peserta) {
                    return [
                        'peserta_id' => $peserta->id,
                        'nama_peserta' => $peserta->nama_lengkap,
                        'nomor_pendaftaran' => $peserta->nomor_pendaftaran,
                        'asal_sekolah' => $peserta->asal_sekolah,
                        'email' => $peserta->email,
                        'cabang_lomba' => [
                            'id' => $peserta->cabangLomba->id ?? null,
                            'nama_cabang' => $peserta->cabangLomba->nama_cabang ?? null,
                        ],
                        'jumlah_token' => $peserta->token->count(),
                        'tokens' => $peserta->token->map(function ($token) {
                            return [
                                'id' => $token->id,
                                'kode_token' => $token->kode_token,
                                'tipe' => $token->tipe,
                                'status_token' => $token->status_token,
                                'created_at' => $token->created_at,
                                'expired_at' => $token->expired_at,
                            ];
                        })
                    ];
                });

            if ($peserta->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Peserta dengan nama tersebut tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data token peserta berhasil ditemukan',
                'data' => $peserta
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencari data token peserta',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function ambilToken(Request $request)
    {
        $pesertaId = $request->input('peserta_id');

        if (!$pesertaId) {
            return response()->json([
                'success' => false,
                'message' => 'Peserta ID tidak ditemukan di request',
            ], 400);
        }

        $peserta = Peserta::with('token')->find($pesertaId);

        if (!$peserta) {
            return response()->json([
                'success' => false,
                'message' => 'Peserta tidak ditemukan',
            ], 404);
        }

        $tokenPeserta = $peserta->token()->where('status_token', 'aktif')->first();

        return response()->json([
            'success' => true,
            'data' => [
                'kode_token' => $tokenPeserta->kode_token ?? null,
                'jumlah_token' => $peserta->token->count(),
            ]
        ]);
    }
}
