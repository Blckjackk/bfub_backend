<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JawabanIsianSingkat extends Model
{
    protected $table = 'jawaban_isian_singkat';
    protected $fillable = [
        'peserta_id',
        'soal_isian_singkat_id',
        'jawaban_peserta',
        'benar',
        'waktu_dijawab'
    ];
    public $timestamps = false;

    // Relasi ke Peserta
    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'peserta_id');
    }

    // Relasi ke SoalIsianSingkat
    public function soalIsianSingkat()
    {
        return $this->belongsTo(SoalIsianSingkat::class, 'soal_isian_singkat_id');
    }
}
