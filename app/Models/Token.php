<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $table = 'token';
    protected $fillable = [
        'kode_token',
        'peserta_id',
        'cabang_lomba_id',
        'tipe',
        'status_token',
        'created_at',
        'expired_at',
        'waktu_digunakan'
    ];
    public $timestamps = false;

    // Relasi ke Peserta
    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'peserta_id');
    }

    // Relasi ke CabangLomba
    public function cabangLomba()
    {
        return $this->belongsTo(CabangLomba::class, 'cabang_lomba_id');
    }
}