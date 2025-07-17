<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jawaban extends Model
{
    protected $table = 'jawaban';
    protected $fillable = [
        'peserta_id',
        'soal_id',
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

    // Relasi ke Soal
    public function soal()
    {
        return $this->belongsTo(Soal::class, 'soal_id');
    }
}