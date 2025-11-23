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
        // Drop tabel lama jika ada (karena strukturnya tidak lengkap)
        Schema::dropIfExists('presensis');
        
        // Buat ulang dengan struktur yang benar
        Schema::create('presensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_kuliah_id')->constrained()->onDelete('cascade');
            $table->foreignId('mahasiswa_id')->constrained()->onDelete('cascade');
            $table->integer('pertemuan'); // Pertemuan ke berapa (1, 2, 3, ...)
            $table->date('tanggal'); // Tanggal pertemuan
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alpa'])->default('alpa');
            $table->text('catatan')->nullable();
            $table->timestamps();

            // Unique constraint: satu mahasiswa tidak bisa absen 2x di pertemuan yang sama
            $table->unique(['jadwal_kuliah_id', 'mahasiswa_id', 'pertemuan'], 'unique_presensi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensis');
    }
};
