<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Integritas relasi 1 mahasiswa per sesi kelas, dan 1 aturan pelanggaran per ujian.
     */
    public function up(): void
    {
        Schema::table('class_attendances', function (Blueprint $table) {
            $table->unique(['class_session_id', 'mahasiswa_id'], 'unique_class_attendance');
        });

        Schema::table('exam_violation_rules', function (Blueprint $table) {
            $table->unique('exam_id', 'unique_exam_violation_rule');
        });
    }

    public function down(): void
    {
        Schema::table('class_attendances', function (Blueprint $table) {
            $table->dropUnique('unique_class_attendance');
        });

        Schema::table('exam_violation_rules', function (Blueprint $table) {
            $table->dropUnique('unique_exam_violation_rule');
        });
    }
};
