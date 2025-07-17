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
        Schema::create('soal', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cabang_lomba_id');
            $table->integer('nomor_soal');
            $table->enum('tipe_soal', ['text', 'gambar'])->default('text');
            $table->text('deskripsi_soal')->nullable();
            $table->text('pertanyaan');
            $table->text('media_soal')->nullable();
            $table->text('opsi_a');
            $table->text('opsi_a_media')->nullable();
            $table->text('opsi_b');
            $table->text('opsi_b_media')->nullable();
            $table->text('opsi_c');
            $table->text('opsi_c_media')->nullable();
            $table->text('opsi_d');
            $table->text('opsi_d_media')->nullable();
            $table->text('opsi_e');
            $table->text('opsi_e_media')->nullable();
            $table->char('jawaban_benar', 1);
            
            // Foreign key constraint
            $table->foreign('cabang_lomba_id')->references('id')->on('cabang_lomba');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soal');
    }
};
