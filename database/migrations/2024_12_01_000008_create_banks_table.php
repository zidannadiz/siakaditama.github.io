<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banks', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama bank
            $table->string('code', 10)->unique(); // Kode bank unik (BCA, BRI, etc)
            $table->string('logo')->nullable(); // Path ke logo bank
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert 10 bank default (matching actual file names)
        DB::table('banks')->insert([
            ['name' => 'BCA', 'code' => 'BCA', 'logo' => 'banks/bca.png', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'BRI', 'code' => 'BRI', 'logo' => 'banks/bri.png', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'BNI', 'code' => 'BNI', 'logo' => 'banks/bni.png', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mandiri', 'code' => 'MANDIRI', 'logo' => 'banks/mandiri.png', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'CIMB Niaga', 'code' => 'CIMB', 'logo' => 'banks/CIMB Niaga.jpg', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Danamon', 'code' => 'DANAMON', 'logo' => 'banks/danamon.png', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Permata', 'code' => 'PERMATA', 'logo' => 'banks/permata.png', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'BSI', 'code' => 'BSI', 'logo' => 'banks/bsi.png', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'OCBC NISP', 'code' => 'OCBC', 'logo' => 'banks/OCBC NISP.jpg', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Maybank', 'code' => 'MAYBANK', 'logo' => 'banks/maybank.png', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('banks');
    }
};

