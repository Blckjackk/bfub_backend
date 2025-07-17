<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JawabanEssay extends Model
{
    protected $table = 'jawaban_essay';
    protected $fillable = [
        'peserta_id',
        'soal_essay_id',
        'jawaban_teks'
    ];
    public $timestamps = false;

    // Relasi ke Peserta
    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'peserta_id');
    }

    // Relasi ke SoalEssay
    public function soalEssay()
    {
        return $this->belongsTo(SoalEssay::class, 'soal_essay_id');
    }
}