<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoalIsianSingkat extends Model
{
    protected $table = 'soal_isian_singkat';
    protected $fillable = [
        'cabang_lomba_id',
        'pertanyaan_isian',
        'jawaban_benar',
        'nomor_soal'
    ];
    public $timestamps = false;

    // Relasi ke CabangLomba
    public function cabangLomba()
    {
        return $this->belongsTo(CabangLomba::class, 'cabang_lomba_id');
    }

    // Relasi ke JawabanIsianSingkat
    public function jawabanIsianSingkat()
    {
        return $this->hasMany(JawabanIsianSingkat::class, 'soal_isian_singkat_id');
    }
}
