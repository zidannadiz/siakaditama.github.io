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
        Schema::create('letter_grades', function (Blueprint $table) {
            $table->id();
            $table->string('letter'); // A, A-, B+, B, etc
            $table->decimal('bobot', 3, 2); // 4.00, 3.75, etc
            $table->integer('min_score'); // 85, 80, 75, etc
            $table->integer('max_score')->nullable(); // 100, 84, 79, etc (null untuk yang teratas)
            $table->integer('order')->default(0); // Untuk urutan sorting
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['letter']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letter_grades');
    }
};
