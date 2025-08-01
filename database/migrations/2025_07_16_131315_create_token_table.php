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
        Schema::create('token', function (Blueprint $table) {
            $table->id();
            $table->string('kode_token', 100)->unique();
            $table->unsignedBigInteger('peserta_id');
            $table->unsignedBigInteger('cabang_lomba_id');
            $table->enum('tipe', ['utama', 'cadangan']);
            $table->enum('status_token', ['aktif', 'digunakan', 'hangus']);
            $table->dateTime('created_at');
            $table->dateTime('expired_at');
            $table->dateTime('waktu_digunakan')->nullable();
            
            // Foreign key constraints
            $table->foreign('peserta_id')->references('id')->on('peserta');
            $table->foreign('cabang_lomba_id')->references('id')->on('cabang_lomba');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('token');
    }
};
