<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('krs_id')->constrained()->onDelete('cascade');
            $table->foreignId('mahasiswa_id')->constrained()->onDelete('cascade');
            $table->foreignId('jadwal_kuliah_id')->constrained()->onDelete('cascade');
            $table->foreignId('dosen_id')->constrained()->onDelete('cascade');
            $table->decimal('nilai_tugas', 5, 2)->nullable();
            $table->decimal('nilai_uts', 5, 2)->nullable();
            $table->decimal('nilai_uas', 5, 2)->nullable();
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->string('huruf_mutu', 2)->nullable(); // A, B+, B, C+, C, D, E
            $table->decimal('bobot', 3, 2)->nullable(); // 4.00, 3.50, etc
            $table->enum('status', ['belum', 'sedang', 'selesai'])->default('belum');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilais');
    }
};

