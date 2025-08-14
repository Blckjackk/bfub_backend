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
        Schema::table('cabang_lomba', function (Blueprint $table) {
            $table->timestamp('tanggal_rilis_nilai')->nullable()->after('waktu_akhir_pengerjaan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cabang_lomba', function (Blueprint $table) {
            $table->dropColumn('tanggal_rilis_nilai');
        });
    }
};
