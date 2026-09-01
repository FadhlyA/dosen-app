<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('front_title')->nullable();  // Gelar depan: Dr., Ir.
            $table->string('back_title')->nullable();   // Gelar belakang: M.Kom, S.T
            $table->string('nip')->nullable();          // Nomor Induk Pegawai
            $table->string('nidn')->nullable();         // Nomor Induk Dosen Nasional
            $table->string('phone')->nullable();        // No HP
            $table->string('study_program')->nullable(); // Program Studi
            $table->string('position')->nullable();     // Jabatan
            $table->string('photo')->nullable();        // Foto dosen
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};