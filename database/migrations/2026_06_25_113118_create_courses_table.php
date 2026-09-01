<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');           // Nama matkul
            $table->string('code');           // Kode matkul (misal: TI101)
            $table->string('class_name');     // Nama kelas (misal: TI-3A)
            $table->string('semester');       // Semester (misal: Ganjil 2024)
            $table->string('access_key')->unique(); // Key untuk mahasiswa
            $table->text('description')->nullable(); // Deskripsi opsional
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};