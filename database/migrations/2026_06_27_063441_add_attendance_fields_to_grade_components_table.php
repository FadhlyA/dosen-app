<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grade_components', function (Blueprint $table) {
            // Apakah komponen ini terhubung ke absensi?
            $table->boolean('is_attendance')->default(false)->after('weight');

            // Formula perhitungan: hadir_only atau include_izin_sakit
            $table->enum('attendance_formula', ['hadir_only', 'include_izin_sakit'])
                  ->default('hadir_only')
                  ->after('is_attendance');
        });
    }

    public function down(): void
    {
        Schema::table('grade_components', function (Blueprint $table) {
            $table->dropColumn(['is_attendance', 'attendance_formula']);
        });
    }
};