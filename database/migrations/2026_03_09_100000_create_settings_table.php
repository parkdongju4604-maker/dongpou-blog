<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('label');
            $table->string('type')->default('text'); // text, textarea, number, color
            $table->string('group')->default('general'); // general, seo, appearance
            $table->timestamps();
        });

        // 기본값 삽입
        $defaults = [
            ['key' => 'blog_name',        'value' => 'DongPou Blog',          'label' => '블로그 이름',        'type' => 'text',     'group' => 'general'],
            ['key' => 'blog_tagline',     'value' => '개인 블로그',            'label' => '블로그 태그라인',    'type' => 'text',     'group' => 'general'],
            ['key' => 'blog_description', 'value' => '다양한 주제의 개인 블로그입니다.',  'label' => '블로그 설명 (SEO)', 'type' => 'textarea', 'group' => 'seo'],
            ['key' => 'hero_title',       'value' => '최신 글',               'label' => '히어로 제목',        'type' => 'text',     'group' => 'general'],
            ['key' => 'hero_subtitle',    'value' => '다양한 주제의 글을 만나보세요.', 'label' => '히어로 부제목', 'type' => 'text', 'group' => 'general'],
            ['key' => 'footer_text',      'value' => 'All rights reserved.',  'label' => '푸터 텍스트',        'type' => 'text',     'group' => 'general'],
            ['key' => 'posts_per_page',   'value' => '9',                     'label' => '페이지당 글 수',     'type' => 'number',   'group' => 'general'],
            ['key' => 'primary_color',    'value' => '#4f46e5',               'label' => '포인트 컬러',        'type' => 'color',    'group' => 'appearance'],
            ['key' => 'og_image',         'value' => '',                      'label' => 'OG 이미지 URL',      'type' => 'text',     'group' => 'seo'],
            ['key' => 'google_analytics', 'value' => '',                      'label' => 'Google Analytics ID','type' => 'text',     'group' => 'seo'],
        ];

        foreach ($defaults as $item) {
            $item['created_at'] = now();
            $item['updated_at'] = now();
            DB::table('settings')->insert($item);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
