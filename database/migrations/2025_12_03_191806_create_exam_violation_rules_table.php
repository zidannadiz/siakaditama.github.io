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
        Schema::create('exam_violation_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            
            // Kriteria pelanggaran yang dapat diatur dosen
            $table->boolean('enable_tab_switch_detection')->default(true);
            $table->integer('max_tab_switch_count')->default(3); // Max jumlah tab switch yang diizinkan
            $table->boolean('terminate_on_tab_switch_limit')->default(true); // Hentikan ujian jika melebihi limit
            
            $table->boolean('enable_copy_paste_detection')->default(true);
            $table->integer('max_copy_paste_count')->default(3);
            $table->boolean('terminate_on_copy_paste_limit')->default(true);
            
            $table->boolean('enable_window_blur_detection')->default(true);
            $table->integer('max_window_blur_count')->default(5);
            $table->boolean('terminate_on_window_blur_limit')->default(false);
            
            $table->boolean('enable_fullscreen_exit_detection')->default(true);
            $table->integer('max_fullscreen_exit_count')->default(3);
            $table->boolean('terminate_on_fullscreen_exit_limit')->default(true);
            
            $table->boolean('enable_multiple_device_detection')->default(false);
            $table->boolean('terminate_on_multiple_device')->default(true);
            
            $table->boolean('enable_time_based_termination')->default(false);
            $table->integer('max_violations_before_termination')->default(3); // Total pelanggaran sebelum dihentikan
            
            $table->text('warning_message')->nullable(); // Pesan peringatan untuk mahasiswa
            $table->text('termination_message')->nullable(); // Pesan saat ujian dihentikan
            
            $table->timestamps();
            
            $table->index('exam_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_violation_rules');
    }
};
