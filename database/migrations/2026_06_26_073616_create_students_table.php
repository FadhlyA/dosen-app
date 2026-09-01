<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('nim');
            $table->string('name');
            $table->string('email')->nullable();
            $table->timestamps();

            // NIM unik per kelas (boleh sama di kelas berbeda)
            $table->unique(['course_id', 'nim']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};