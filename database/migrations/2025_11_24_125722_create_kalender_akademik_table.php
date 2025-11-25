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
        Schema::create('kalender_akademik', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable(); // Nullable untuk event satu hari
            $table->time('jam_mulai')->nullable(); // Optional untuk event dengan waktu spesifik
            $table->time('jam_selesai')->nullable();
            $table->enum('jenis', [
                'semester', 
                'krs', 
                'pembayaran', 
                'ujian', 
                'libur', 
                'kegiatan', 
                'pengumuman',
                'lainnya'
            ])->default('lainnya');
            $table->enum('target_role', ['semua', 'admin', 'dosen', 'mahasiswa'])->default('semua');
            $table->foreignId('semester_id')->nullable()->constrained('semesters')->onDelete('cascade');
            $table->string('warna')->default('#3B82F6'); // Warna untuk tampilan kalender
            $table->boolean('is_important')->default(false); // Event penting (deadline, dll)
            $table->text('link')->nullable(); // Link ke halaman terkait (opsional)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kalender_akademik');
    }
};
