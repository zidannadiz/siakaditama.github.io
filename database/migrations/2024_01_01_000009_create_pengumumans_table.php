<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengumumans', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('isi');
            $table->enum('kategori', ['umum', 'akademik', 'beasiswa', 'kegiatan'])->default('umum');
            $table->enum('target', ['semua', 'mahasiswa', 'dosen', 'admin'])->default('semua');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Pembuat pengumuman
            $table->boolean('is_pinned')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumumans');
    }
};

