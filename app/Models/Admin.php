<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table = 'admin';
    protected $fillable = ['nama_admin', 'password_hash', 'role'];
    public $timestamps = false;
}