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
        Schema::create('jawaban_isian_singkat', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('peserta_id');
            $table->unsignedBigInteger('soal_isian_singkat_id');
            $table->string('jawaban_peserta')->nullable();
            $table->boolean('benar')->default(false);
            $table->integer('score')->default(0); // Tambah kolom score
            $table->timestamp('waktu_dijawab')->nullable();
            
            // Foreign key constraints
            $table->foreign('peserta_id')->references('id')->on('peserta');
            $table->foreign('soal_isian_singkat_id')->references('id')->on('soal_isian_singkat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jawaban_isian_singkat');
    }
};
