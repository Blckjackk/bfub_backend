<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('peserta', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap', 100);
            $table->string('nomor_pendaftaran', 50);
            $table->string('asal_sekolah', 100);
            $table->string('username', 100);
            $table->string('role', 50)->default('peserta'); // Default role is 'peserta'
            $table->string('password_hash', 255);
            $table->unsignedBigInteger('cabang_lomba_id');
            $table->string('status_ujian', 20);
            $table->dateTime('waktu_mulai');
            $table->dateTime('waktu_selesai');
            $table->float('nilai_total')->nullable();
            $table->integer('waktu_pengerjaan_total')->nullable();
    
            // Foreign key constraint
            $table->foreign('cabang_lomba_id')->references('id')->on('cabang_lomba');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peserta');
    }
};
