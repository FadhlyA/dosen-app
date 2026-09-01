<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('institution_name')->nullable()->after('photo');     // Nama Kampus
            $table->text('institution_address')->nullable()->after('institution_name'); // Alamat
            $table->string('institution_email')->nullable()->after('institution_address'); // Email
            $table->string('institution_website')->nullable()->after('institution_email'); // Website
            $table->string('institution_phone')->nullable()->after('institution_website'); // No Telp
            $table->string('institution_logo')->nullable()->after('institution_phone');   // Logo
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn([
                'institution_name',
                'institution_address',
                'institution_email',
                'institution_website',
                'institution_phone',
                'institution_logo',
            ]);
        });
    }
};