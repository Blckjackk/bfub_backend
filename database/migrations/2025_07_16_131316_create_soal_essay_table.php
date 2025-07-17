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
        Schema::create('soal_essay', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cabang_lomba_id');
            $table->text('pertanyaan_essay');
            
            // Foreign key constraint
            $table->foreign('cabang_lomba_id')->references('id')->on('cabang_lomba');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soal_essay');
    }
};
