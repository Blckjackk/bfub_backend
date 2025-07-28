<?php

namespace App\Http\Controllers;

use App\Models\Peserta;
use Illuminate\Http\Request;


class PesertaController extends Controller
{
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
