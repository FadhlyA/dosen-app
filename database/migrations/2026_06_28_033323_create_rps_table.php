<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('title');           // Judul RPS
            $table->string('file_path');       // Path file RPS
            $table->string('original_name');   // Nama file asli
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rps');
    }
};