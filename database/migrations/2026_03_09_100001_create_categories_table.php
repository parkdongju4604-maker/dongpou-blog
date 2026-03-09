<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 기본 카테고리
        $defaults = [
            ['name' => '일반',     'slug' => 'general',    'description' => '일반 글',       'sort_order' => 1],
            ['name' => '개발',     'slug' => 'dev',        'description' => '개발 관련 글',   'sort_order' => 2],
            ['name' => '일상',     'slug' => 'daily',      'description' => '일상 이야기',    'sort_order' => 3],
            ['name' => '리뷰',     'slug' => 'review',     'description' => '제품/서비스 리뷰', 'sort_order' => 4],
        ];

        foreach ($defaults as $item) {
            $item['created_at'] = now();
            $item['updated_at'] = now();
            DB::table('categories')->insert($item);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
