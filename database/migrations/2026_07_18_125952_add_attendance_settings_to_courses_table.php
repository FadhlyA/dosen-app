<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Formula absensi: hadir_only atau include_izin_sakit
            $table->enum('attendance_formula', ['hadir_only', 'include_izin_sakit'])
                  ->default('hadir_only')->after('access_key');
            // Batas minimum kehadiran (default 75%)
            $table->float('attendance_threshold')->default(75)->after('attendance_formula');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['attendance_formula', 'attendance_threshold']);
        });
    }
};