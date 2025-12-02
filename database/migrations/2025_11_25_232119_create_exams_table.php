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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_kuliah_id')->constrained('jadwal_kuliahs')->onDelete('cascade');
            $table->foreignId('dosen_id')->constrained('dosens')->onDelete('cascade');
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->enum('tipe', ['pilgan', 'essay', 'campuran'])->default('pilgan');
            $table->integer('durasi'); // Durasi dalam menit
            $table->timestamp('mulai')->nullable(); // Waktu mulai ujian (null = bisa mulai kapan saja)
            $table->timestamp('selesai'); // Deadline ujian
            $table->integer('total_soal')->default(0);
            $table->decimal('bobot', 5, 2)->default(0); // Bobot nilai (0-100)
            $table->boolean('random_soal')->default(false); // Random urutan soal
            $table->boolean('random_pilihan')->default(false); // Random pilihan jawaban (untuk pilgan)
            $table->boolean('tampilkan_nilai')->default(true); // Tampilkan nilai setelah selesai
            $table->boolean('prevent_copy_paste')->default(true); // Prevent copy paste
            $table->boolean('prevent_new_tab')->default(true); // Prevent membuka tab baru
            $table->boolean('fullscreen_mode')->default(true); // Paksa fullscreen mode
            $table->enum('status', ['draft', 'published', 'ongoing', 'finished'])->default('draft');
            $table->timestamps();
            
            $table->index('jadwal_kuliah_id');
            $table->index('mulai');
            $table->index('selesai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
