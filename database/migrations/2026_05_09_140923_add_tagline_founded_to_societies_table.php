<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('societies', function (Blueprint $table) {
            $table->string('tagline')->nullable()->after('description');
            $table->string('founded_at')->nullable()->after('tagline');
            $table->string('cover_image')->nullable()->after('founded_at');
        });
    }

    public function down(): void
    {
        Schema::table('societies', function (Blueprint $table) {
            $table->dropColumn(['tagline', 'founded_at', 'cover_image']);
        });
    }
};