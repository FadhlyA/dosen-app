<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->text('note_before')->nullable()->after('status'); // Note sebelum pertemuan
            $table->text('note_after')->nullable()->after('note_before');  // Note sesudah pertemuan
        });
    }

    public function down(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropColumn(['note_before', 'note_after']);
        });
    }
};