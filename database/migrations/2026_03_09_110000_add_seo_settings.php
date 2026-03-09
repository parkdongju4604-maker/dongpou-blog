<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $extras = [
            ['key' => 'google_site_verification', 'value' => '', 'label' => 'Google Search Console 인증 코드', 'type' => 'text', 'group' => 'verification'],
            ['key' => 'naver_site_verification',  'value' => '', 'label' => '네이버 서치어드바이저 인증 코드',   'type' => 'text', 'group' => 'verification'],
            ['key' => 'twitter_handle',           'value' => '', 'label' => 'Twitter/X 계정 (@없이)',         'type' => 'text', 'group' => 'seo'],
            ['key' => 'og_image_default',         'value' => '', 'label' => '기본 OG 이미지 URL',             'type' => 'text', 'group' => 'seo'],
            ['key' => 'author_name',              'value' => '', 'label' => '기본 저자 이름',                 'type' => 'text', 'group' => 'seo'],
            ['key' => 'robots_index',             'value' => 'index,follow', 'label' => '검색엔진 수집 설정', 'type' => 'text', 'group' => 'seo'],
        ];

        foreach ($extras as $item) {
            // 이미 존재하면 건너뜀
            if (!DB::table('settings')->where('key', $item['key'])->exists()) {
                $item['created_at'] = now();
                $item['updated_at'] = now();
                DB::table('settings')->insert($item);
            }
        }
    }

    public function down(): void
    {
        $keys = ['google_site_verification','naver_site_verification','twitter_handle','og_image_default','author_name','robots_index'];
        DB::table('settings')->whereIn('key', $keys)->delete();
    }
};
