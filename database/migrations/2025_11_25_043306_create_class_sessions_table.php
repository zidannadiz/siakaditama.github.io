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
        Schema::create('class_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_kuliah_id')->constrained()->onDelete('cascade');
            $table->foreignId('dosen_id')->constrained()->onDelete('cascade');
            $table->integer('pertemuan');
            $table->date('tanggal');
            $table->string('kode_kelas', 8)->unique(); // Kode unik untuk join kelas
            $table->enum('status', ['buka', 'tutup'])->default('buka');
            $table->timestamp('started_at');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_sessions');
    }
};
