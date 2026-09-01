<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained()->onDelete('cascade');
            $table->string('title');          // Judul materi
            $table->enum('type', ['file', 'link']); // Tipe: file upload atau link
            $table->string('file_path')->nullable(); // Path file (PDF/PPT)
            $table->string('link_url')->nullable();  // URL Google Drive / YouTube
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};