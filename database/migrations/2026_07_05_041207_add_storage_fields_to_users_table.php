<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Storage limit dalam bytes (default 200MB)
            $table->bigInteger('storage_limit')->default(209715200)->after('remember_token');
            // Storage terpakai dalam bytes
            $table->bigInteger('storage_used')->default(0)->after('storage_limit');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['storage_limit', 'storage_used']);
        });
    }
};