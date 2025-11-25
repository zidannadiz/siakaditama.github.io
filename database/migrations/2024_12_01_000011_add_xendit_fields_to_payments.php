<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('xendit_id')->nullable()->unique()->after('virtual_account');
            $table->string('external_id')->nullable()->after('xendit_id');
            $table->text('xendit_response')->nullable()->after('external_id');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['xendit_id', 'external_id', 'xendit_response']);
        });
    }
};

