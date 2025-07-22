<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peserta extends Model
{
    protected $table = 'peserta';
    protected $fillable = [
        'nama_lengkap',
        'nomor_pendaftaran',
        'asal_sekolah',
        'username',
        'role',
        'password_hash',
        'cabang_lomba_id',
        'status_ujian',
        'waktu_mulai',
        'waktu_selesai',
        'nilai_total',
        'waktu_pengerjaan_total'
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
        return $this->hasMany(Jawaban::class, 'peserta_id');
    }

    // Relasi ke JawabanEssay
    public function jawabanEssay()
    {
        return $this->hasMany(JawabanEssay::class, 'peserta_id');
    }

    // Relasi ke Token
    public function token()
    {
        return $this->hasMany(Token::class, 'peserta_id');
    }
}