<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoalEssay extends Model
{
    protected $table = 'soal_essay';
    protected $fillable = [
        'cabang_lomba_id',
        'nomor_soal',
        'pertanyaan_essay'
    ];
    public $timestamps = false;

    // Relasi ke CabangLomba
    public function cabangLomba()
    {
        return $this->belongsTo(CabangLomba::class, 'cabang_lomba_id');
    }

    // Relasi ke JawabanEssay
    public function jawabanEssay()
    {
        return $this->hasMany(JawabanEssay::class, 'soal_essay_id');
    }
}