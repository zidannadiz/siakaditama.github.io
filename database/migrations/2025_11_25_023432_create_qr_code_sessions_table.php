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
        Schema::create('qr_code_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_kuliah_id')->constrained()->onDelete('cascade');
            $table->integer('pertemuan'); // Pertemuan ke berapa
            $table->date('tanggal'); // Tanggal pertemuan
            $table->string('token', 100)->unique(); // Token unik untuk QR code
            $table->timestamp('expires_at'); // Waktu kadaluarsa QR code
            $table->boolean('is_active')->default(true); // Status aktif/tidak aktif
            $table->integer('duration_minutes')->default(30); // Durasi valid QR code dalam menit
            $table->timestamps();

            // Index untuk performa query
            $table->index(['token', 'is_active', 'expires_at']);
            $table->index(['jadwal_kuliah_id', 'pertemuan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qr_code_sessions');
    }
};
