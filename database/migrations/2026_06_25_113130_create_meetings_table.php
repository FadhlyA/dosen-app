<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->integer('meeting_number'); // Pertemuan ke-berapa
            $table->string('title');           // Judul pertemuan
            $table->text('description')->nullable(); // Catatan/rencana
            $table->date('meeting_date');      // Tanggal pertemuan
            $table->enum('status', ['upcoming', 'done'])->default('upcoming');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};