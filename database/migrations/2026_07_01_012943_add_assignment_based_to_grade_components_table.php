<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grade_components', function (Blueprint $table) {
            $table->boolean('is_assignment_based')->default(false)->after('attendance_formula');
        });
    }

    public function down(): void
    {
        Schema::table('grade_components', function (Blueprint $table) {
            $table->dropColumn('is_assignment_based');
        });
    }
};