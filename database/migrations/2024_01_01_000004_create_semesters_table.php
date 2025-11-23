<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->string('nama_semester'); // Contoh: "Semester Ganjil 2024/2025"
            $table->enum('jenis', ['ganjil', 'genap']); // ganjil atau genap
            $table->year('tahun_ajaran');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->enum('status', ['aktif', 'nonaktif'])->default('nonaktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semesters');
    }
};

