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
