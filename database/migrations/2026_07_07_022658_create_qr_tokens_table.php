<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained()->onDelete('cascade');
            $table->string('token', 64)->unique();
            $table->integer('scan_count')->default(0);  // jumlah yang sudah scan
            $table->integer('max_scan')->default(2);    // max 2 scan per token
            $table->timestamp('expires_at');            // expired setelah 1 menit
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_tokens');
    }
};