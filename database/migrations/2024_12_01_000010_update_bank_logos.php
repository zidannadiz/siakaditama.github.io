<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update logo paths to match actual file names
        DB::table('banks')->where('code', 'CIMB')->update([
            'logo' => 'banks/CIMB Niaga.jpg',
            'updated_at' => now(),
        ]);

        DB::table('banks')->where('code', 'OCBC')->update([
            'logo' => 'banks/OCBC NISP.jpg',
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        // Revert to original paths
        DB::table('banks')->where('code', 'CIMB')->update([
            'logo' => 'banks/cimb.png',
            'updated_at' => now(),
        ]);

        DB::table('banks')->where('code', 'OCBC')->update([
            'logo' => 'banks/ocbc.png',
            'updated_at' => now(),
        ]);
    }
};

