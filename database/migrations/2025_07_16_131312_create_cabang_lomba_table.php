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
        Schema::create('cabang_lomba', function (Blueprint $table) {
            $table->id();
            $table->string('nama_cabang', 100);
            $table->text('deskripsi_lomba')->nullable();
            $table->dateTime('waktu_mulai_pengerjaan');
            $table->dateTime('waktu_akhir_pengerjaan');
            $table->timestamps(); // Add timestamps for created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cabang_lomba');
    }
};
