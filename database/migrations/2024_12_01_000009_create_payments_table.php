<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique(); // Nomor invoice unik
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('bank_id')->constrained('banks')->onDelete('cascade');
            $table->string('virtual_account')->unique(); // Nomor virtual account
            $table->string('payment_type'); // SPP, UKT, BIAYA_PENDAFTARAN, dll
            $table->decimal('amount', 15, 2); // Jumlah pembayaran
            $table->decimal('fee', 15, 2)->default(0); // Biaya admin
            $table->decimal('total_amount', 15, 2); // Total yang harus dibayar (amount + fee)
            $table->enum('status', ['pending', 'paid', 'expired', 'cancelled'])->default('pending');
            $table->text('description')->nullable(); // Keterangan pembayaran
            $table->timestamp('expired_at'); // Batas waktu pembayaran
            $table->timestamp('paid_at')->nullable(); // Waktu pembayaran
            $table->text('payment_proof')->nullable(); // Bukti pembayaran (jika manual)
            $table->text('notes')->nullable(); // Catatan admin
            $table->timestamps();

            $table->index('invoice_number');
            $table->index('virtual_account');
            $table->index('status');
            $table->index('expired_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

