<?php

namespace App\Http\Controllers;

use App\Models\Peserta;
use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;


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

            // Cek apakah peserta sudah memiliki token yang sedang digunakan
            $activeToken = Token::where('peserta_id', $request->peserta_id)
                              ->where('status_token', 'digunakan')
                              ->first();

            if ($activeToken) {
                return response()->json([
                    'success' => true,
                    'message' => 'Peserta sudah memiliki token aktif',
                    'data' => [
                        'token' => $activeToken->kode_token,
                        'waktu_mulai' => $activeToken->waktu_digunakan
                    ]
                ]);
            }

            // Cek apakah token ada dan merupakan token utama
            $token = Token::where('kode_token', $request->kode_token)
                         ->where('tipe', 'utama')
                         ->first();
            
            if (!$token) {
                Log::warning('Token not found or not primary:', ['kode_token' => $request->kode_token]);
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak valid atau bukan token utama'
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

            // Update status ujian peserta menjadi 'sedang_ujian'
            $peserta = Peserta::find($request->peserta_id);
            $peserta->status_ujian = 'sedang_ujian';
            $peserta->waktu_mulai = $now;
            
            if (!$peserta->save()) {
                throw new \Exception('Gagal mengupdate status ujian peserta');
            }
            
            Log::info('Token successfully used and exam status updated:', [
                'token_id' => $token->id,
                'kode_token' => $token->kode_token,
                'waktu_digunakan' => $token->waktu_digunakan,
                'peserta_id' => $peserta->id,
                'status_ujian' => $peserta->status_ujian
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

    // 8. Selesaikan ujian peserta
    public function selesaikanUjian(Request $request)
    {
        try {
            Log::info('Selesaikan ujian request:', $request->all());
            
            $request->validate([
                'peserta_id' => 'required|exists:peserta,id'
            ]);

            $peserta = Peserta::find($request->peserta_id);
            Log::info('Found peserta:', [
                'id' => $peserta->id,
                'nama' => $peserta->nama_lengkap,
                'status_ujian' => $peserta->status_ujian
            ]);
            
            // Pastikan peserta sedang dalam ujian
            if ($peserta->status_ujian !== 'sedang_ujian') {
                Log::warning('Peserta not in exam:', [
                    'peserta_id' => $peserta->id,
                    'current_status' => $peserta->status_ujian
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Peserta tidak sedang dalam ujian. Status saat ini: ' . $peserta->status_ujian
                ], 400);
            }

            $now = now();
            $peserta->status_ujian = 'selesai';
            $peserta->waktu_selesai = $now;
            
            // Hitung waktu pengerjaan total dalam menit
            if ($peserta->waktu_mulai) {
                $waktuMulai = Carbon::parse($peserta->waktu_mulai);
                $waktuSelesai = Carbon::parse($now);
                $peserta->waktu_pengerjaan_total = $waktuSelesai->diffInMinutes($waktuMulai);
            }
            
            if (!$peserta->save()) {
                throw new \Exception('Gagal mengupdate status ujian peserta');
            }

            // Hanguskan token yang sedang digunakan
            $activeToken = Token::where('peserta_id', $request->peserta_id)
                              ->where('status_token', 'digunakan')
                              ->first();
            
            if ($activeToken) {
                $activeToken->status_token = 'hangus';
                $activeToken->save();
            }
            
            Log::info('Exam completed:', [
                'peserta_id' => $peserta->id,
                'status_ujian' => $peserta->status_ujian,
                'waktu_mulai' => $peserta->waktu_mulai,
                'waktu_selesai' => $peserta->waktu_selesai,
                'waktu_pengerjaan_total' => $peserta->waktu_pengerjaan_total
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ujian berhasil diselesaikan',
                'data' => [
                    'status_ujian' => $peserta->status_ujian,
                    'waktu_mulai' => $peserta->waktu_mulai,
                    'waktu_selesai' => $peserta->waktu_selesai,
                    'waktu_pengerjaan_total' => $peserta->waktu_pengerjaan_total
                ]
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error completing exam: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyelesaikan ujian',
                'debug_message' => $e->getMessage()
            ], 500);
        }
    }

    // 9. Hanguskan token peserta 
    public function hanguskanToken(Request $request)
    {
        try {
            $request->validate([
                'kode_token' => 'required|string'
            ]);

            // Cari token berdasarkan kode
            $token = Token::where('kode_token', $request->kode_token)->first();
            
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak ditemukan'
                ], 404);
            }

            // Update status token menjadi hangus
            $token->status_token = 'hangus';
            $token->save();
            
            Log::info('Token successfully expired:', [
                'token_id' => $token->id,
                'kode_token' => $token->kode_token,
                'peserta_id' => $token->peserta_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Token berhasil dihanguskan'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error expiring token: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghanguskan token'
            ], 500);
        }
    }

    /**
     * Heartbeat untuk tracking peserta online
     */
    public function heartbeat(Request $request)
    {
        try {
            $request->validate([
                'peserta_id' => 'required|exists:peserta,id'
            ]);

            $peserta = Peserta::find($request->peserta_id);
            if ($peserta) {
                // Update timestamp to mark as online
                $peserta->touch(); // Updates updated_at timestamp
                
                return response()->json([
                    'success' => true,
                    'message' => 'Heartbeat received',
                    'timestamp' => now()
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Peserta not found'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Heartbeat failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
