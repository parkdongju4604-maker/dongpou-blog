<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // ── general ──
            ['key' => 'blog_name',        'value' => 'DongPou Blog',                    'label' => '블로그 이름',        'type' => 'text',     'group' => 'general'],
            ['key' => 'blog_tagline',     'value' => '개인 블로그',                       'label' => '블로그 태그라인',    'type' => 'text',     'group' => 'general'],
            ['key' => 'hero_title',       'value' => '최신 글',                           'label' => '히어로 제목',        'type' => 'text',     'group' => 'general'],
            ['key' => 'hero_subtitle',    'value' => '다양한 주제의 글을 만나보세요.',       'label' => '히어로 부제목',      'type' => 'text',     'group' => 'general'],
            ['key' => 'footer_text',      'value' => 'All rights reserved.',              'label' => '푸터 텍스트',        'type' => 'text',     'group' => 'general'],
            ['key' => 'posts_per_page',   'value' => '9',                                 'label' => '페이지당 글 수',     'type' => 'number',   'group' => 'general'],

            // ── appearance ──
            ['key' => 'primary_color',    'value' => '#4f46e5',                            'label' => '포인트 컬러',        'type' => 'color',    'group' => 'appearance'],

            // ── seo ──
            ['key' => 'blog_description', 'value' => '다양한 주제의 개인 블로그입니다.',     'label' => '블로그 설명 (SEO)(공백포함 80자 이내)', 'type' => 'textarea', 'group' => 'seo'],
            ['key' => 'og_image',         'value' => '',                                   'label' => 'OG 이미지 URL',      'type' => 'text',     'group' => 'seo'],
            ['key' => 'og_image_default', 'value' => '',                                   'label' => '기본 OG 이미지 URL', 'type' => 'text',     'group' => 'seo'],
            ['key' => 'google_analytics', 'value' => '',                                   'label' => 'Google Analytics ID','type' => 'text',     'group' => 'seo'],
            ['key' => 'twitter_handle',   'value' => '',                                   'label' => 'Twitter/X 계정 (@없이)', 'type' => 'text', 'group' => 'seo'],
            ['key' => 'author_name',      'value' => '',                                   'label' => '기본 저자 이름',     'type' => 'text',     'group' => 'seo'],
            ['key' => 'author_nickname',  'value' => '',                                   'label' => '블로그 운영자 닉네임', 'type' => 'text',   'group' => 'seo'],
            ['key' => 'author_description','value' => '',                                   'label' => '운영자 설명 (JSON-LD)(공백포함 80자 이내)', 'type' => 'textarea','group' => 'seo'],
            ['key' => 'author_slug',      'value' => '',                                   'label' => '작성자 아카이브 슬러그', 'type' => 'text',   'group' => 'seo'],
            ['key' => 'robots_index',     'value' => 'index,follow',                       'label' => '검색엔진 수집 설정', 'type' => 'text',     'group' => 'seo'],
            ['key' => 'meta_keywords',    'value' => '',                                   'label' => '사이트 키워드 (쉼표 구분)', 'type' => 'text', 'group' => 'seo'],
            ['key' => 'head_code',        'value' => '',                                   'label' => '추가 Head 코드 (meta 태그 직접 입력)', 'type' => 'textarea', 'group' => 'seo'],
            ['key' => 'kakao_js_key',     'value' => '',                                   'label' => '카카오톡 공유 JavaScript 키', 'type' => 'text', 'group' => 'seo'],

            // ── verification ──
            ['key' => 'google_site_verification', 'value' => '', 'label' => 'Google Search Console 인증 코드',       'type' => 'text', 'group' => 'verification'],
            ['key' => 'naver_site_verification',  'value' => '', 'label' => '네이버 서치어드바이저 인증 코드',          'type' => 'text', 'group' => 'verification'],
        ];

        foreach ($settings as $item) {
            DB::table('settings')->updateOrInsert(
                ['key' => $item['key']],
                array_merge($item, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
