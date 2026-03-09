<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('meta_title',       255)->nullable()->after('published_at');
            $table->string('meta_description', 500)->nullable()->after('meta_title');
            $table->string('meta_keywords',    255)->nullable()->after('meta_description');
            $table->string('og_image',         500)->nullable()->after('meta_keywords');
            $table->boolean('noindex')->default(false)->after('og_image');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description', 'meta_keywords', 'og_image', 'noindex']);
        });
    }
};
