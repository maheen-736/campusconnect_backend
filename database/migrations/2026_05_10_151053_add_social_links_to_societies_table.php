<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('societies', function (Blueprint $table) {
            $table->string('instagram')->nullable()->after('cover_image');
            $table->string('facebook')->nullable()->after('instagram');
            $table->string('linkedin')->nullable()->after('facebook');
            $table->string('tiktok')->nullable()->after('linkedin');
            $table->string('twitter')->nullable()->after('tiktok');
            $table->string('whatsapp')->nullable()->after('twitter');
        });
    }

    public function down(): void
    {
        Schema::table('societies', function (Blueprint $table) {
            $table->dropColumn(['instagram','facebook','linkedin','tiktok','twitter','whatsapp']);
        });
    }
};