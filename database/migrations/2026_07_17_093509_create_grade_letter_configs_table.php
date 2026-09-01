<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_letter_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('letter');        // A, B, C, D
            $table->float('min_score');      // nilai minimum
            $table->float('max_score');      // nilai maksimum
            $table->integer('order');        // urutan (1=A, 2=B, dst)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_letter_configs');
    }
};