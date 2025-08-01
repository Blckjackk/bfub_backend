<?php

namespace App\Http\Controllers;

use App\Models\Peserta;
use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;


class PesertaController extends Controller
{
    /**
     * Menggunakan token untuk memulai ujian
     */
    public function pakaiToken(Request $request)
    {
        try {
            Log::info('Received token request:', $request->all());
            
            $request->validate([
                'kode_token' => 'required|string',
                'peserta_id' => 'required|exists:peserta,id',
                'cabang_lomba_id' => 'required|exists:cabang_lomba,id'
            ]);

            // Cek apakah token ada
            $token = Token::where('kode_token', $request->kode_token)->first();
            
            if (!$token) {
                Log::warning('Token not found:', ['kode_token' => $request->kode_token]);
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak ditemukan'
                ], 400);
            }

            // Cek kepemilikan token
            if ($token->peserta_id != $request->peserta_id) {
                Log::warning('Token ownership mismatch', [
                    'token_peserta_id' => $token->peserta_id,
                    'request_peserta_id' => $request->peserta_id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Token ini tidak terdaftar untuk peserta ini'
                ], 400);
            }

            // Cek cabang lomba
            if ($token->cabang_lomba_id != $request->cabang_lomba_id) {
                Log::warning('Token cabang lomba mismatch', [
                    'token_cabang_lomba_id' => $token->cabang_lomba_id,
                    'request_cabang_lomba_id' => $request->cabang_lomba_id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Token ini tidak valid untuk cabang lomba ini'
                ], 400);
            }

            // Cek status token
            if ($token->status_token !== 'aktif') {
                Log::warning('Token status not active', [
                    'status' => $token->status_token,
                    'token' => $token
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Token sudah ' . $token->status_token
                ], 400);
            }

            // Update status token menjadi 'digunakan'
            $now = now();
            $token->status_token = 'digunakan';
            $token->waktu_digunakan = $now;
            
            if (!$token->save()) {
                throw new \Exception('Gagal mengupdate status token');
            }
            
            Log::info('Token successfully used:', [
                'token_id' => $token->id,
                'kode_token' => $token->kode_token,
                'waktu_digunakan' => $token->waktu_digunakan
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Token berhasil digunakan',
                'data' => [
                    'token' => $token->kode_token,
                    'waktu_mulai' => $token->waktu_digunakan
                ]
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Token processing error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid atau sudah tidak aktif. Silakan periksa kembali token Anda.',
                'debug_message' => $e->getMessage() // Hanya untuk development
            ], 400);
        }
    }
    public function getDataPeserta()
    {
        $peserta = Peserta::all();
        return response()->json($peserta);
    }

    // 5. Data peserta saat ini (profil)
    public function me(Request $request)
    {
        $request->validate([
            'peserta_id' => 'required|exists:peserta,id'
        ]);

        $peserta = Peserta::with(['cabangLomba', 'token'])->find($request->peserta_id);

        return response()->json([
            'success' => true,
            'message' => 'Data peserta berhasil diambil',
            'data' => $peserta
        ]);
    }

    // 6. Update data peserta
    public function update(Request $request)
    {
        $request->validate([
            'peserta_id' => 'required|exists:peserta,id',
            'nama_lengkap' => 'sometimes|string|max:100',
            'asal_sekolah' => 'sometimes|string|max:100',
            'email' => 'sometimes|email|max:100'
        ]);

        $peserta = Peserta::find($request->peserta_id);
        
        $peserta->update($request->only([
            'nama_lengkap',
            'asal_sekolah',
            'email'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Data peserta berhasil diupdate',
            'data' => $peserta
        ]);
    }

    // Get profile peserta by ID dengan cabang lomba
    public function getProfile($id)
    {
        try {
            $peserta = Peserta::with('cabangLomba')->find($id);

            if (!$peserta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Peserta tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data profile peserta berhasil diambil',
                'data' => [
                    'id' => $peserta->id,
                    'nama_lengkap' => $peserta->nama_lengkap,
                    'nomor_pendaftaran' => $peserta->nomor_pendaftaran,
                    'asal_sekolah' => $peserta->asal_sekolah,
                    'kota_provinsi' => $peserta->kota_provinsi,
                    'username' => $peserta->username,
                    'status_ujian' => $peserta->status_ujian,
                    'waktu_mulai' => $peserta->waktu_mulai,
                    'waktu_selesai' => $peserta->waktu_selesai,
                    'nilai_total' => $peserta->nilai_total,
                    'cabang_lomba' => $peserta->cabangLomba ? [
                        'id' => $peserta->cabangLomba->id,
                        'nama_cabang' => $peserta->cabangLomba->nama_cabang,
                        'deskripsi' => $peserta->cabangLomba->deskripsi_lomba,
                        'waktu_mulai_pengerjaan' => $peserta->cabangLomba->waktu_mulai_pengerjaan,
                        'waktu_akhir_pengerjaan' => $peserta->cabangLomba->waktu_akhir_pengerjaan,
                    ] : null
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data peserta',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 7. Status ujian peserta
    public function statusUjian(Request $request)
    {
        $request->validate([
            'peserta_id' => 'required|exists:peserta,id'
        ]);

        $peserta = Peserta::with('cabangLomba')->find($request->peserta_id);

        return response()->json([
            'success' => true,
            'message' => 'Status ujian peserta',
            'data' => [
                'status_ujian' => $peserta->status_ujian,
                'waktu_mulai' => $peserta->waktu_mulai,
                'waktu_selesai' => $peserta->waktu_selesai,
                'nilai_total' => $peserta->nilai_total,
                'waktu_pengerjaan_total' => $peserta->waktu_pengerjaan_total,
                'cabang_lomba' => $peserta->cabangLomba
            ]
        ]);
    }
}
