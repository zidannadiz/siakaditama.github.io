<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nim', 20)->unique();
            $table->string('nama');
            $table->foreignId('prodi_id')->constrained()->onDelete('cascade');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_hp', 15)->nullable();
            $table->integer('semester')->default(1);
            $table->enum('status', ['aktif', 'nonaktif', 'lulus'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mahasiswas');
    }
};

