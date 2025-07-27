<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CabangLomba extends Model
{
    protected $table = 'cabang_lomba';
    protected $fillable = [
        'nama_cabang',
        'deskripsi_lomba',
        'waktu_mulai_pengerjaan',
        'waktu_akhir_pengerjaan'
    ];
    public $timestamps = true; // Enable timestamps

    // Relasi ke Peserta
    public function peserta()
    {
        return $this->hasMany(Peserta::class, 'cabang_lomba_id');
    }

    // Relasi ke Soal
    public function soal()
    {
        return $this->hasMany(Soal::class, 'cabang_lomba_id');
    }

    // Relasi ke SoalEssay
    public function soalEssay()
    {
        return $this->hasMany(SoalEssay::class, 'cabang_lomba_id');
    }

    // Relasi ke SoalIsianSingkat
    public function soalIsianSingkat()
    {
        return $this->hasMany(SoalIsianSingkat::class, 'cabang_lomba_id');
    }
}