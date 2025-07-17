<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Soal extends Model
{
    protected $table = 'soal';
    protected $fillable = [
        'cabang_lomba_id',
        'nomor_soal',
        'tipe_soal',
        'deskripsi_soal',
        'pertanyaan',
        'media_soal',
        'opsi_a',
        'opsi_a_media',
        'opsi_b',
        'opsi_b_media',
        'opsi_c',
        'opsi_c_media',
        'opsi_d',
        'opsi_d_media',
        'opsi_e',
        'opsi_e_media',
        'jawaban_benar'
    ];
    public $timestamps = false;

    // Relasi ke CabangLomba
    public function cabangLomba()
    {
        return $this->belongsTo(CabangLomba::class, 'cabang_lomba_id');
    }

    // Relasi ke Jawaban
    public function jawaban()
    {
        return $this->hasMany(Jawaban::class, 'soal_id');
    }
}