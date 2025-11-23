<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('krs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained()->onDelete('cascade');
            $table->foreignId('jadwal_kuliah_id')->constrained()->onDelete('cascade');
            $table->foreignId('semester_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->text('catatan')->nullable();
            $table->timestamps();
            
            // Prevent duplicate KRS entries
            $table->unique(['mahasiswa_id', 'jadwal_kuliah_id', 'semester_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('krs');
    }
};

