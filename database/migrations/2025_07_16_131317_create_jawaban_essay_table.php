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
        Schema::create('jawaban_essay', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('peserta_id');
            $table->unsignedBigInteger('soal_essay_id');
            $table->text('jawaban_teks');
            
            // Foreign key constraints
            $table->foreign('peserta_id')->references('id')->on('peserta');
            $table->foreign('soal_essay_id')->references('id')->on('soal_essay');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jawaban_essay');
    }
};
