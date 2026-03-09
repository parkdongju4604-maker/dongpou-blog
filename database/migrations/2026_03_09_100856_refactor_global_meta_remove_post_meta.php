<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. posts 테이블에서 per-post 메타 컬럼 제거
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description', 'meta_keywords', 'og_image', 'noindex']);
        });

        // 2. 전역 메타 설정 추가
        $extras = [
            [
                'key'   => 'meta_keywords',
                'value' => '',
                'label' => '사이트 키워드 (쉼표 구분)',
                'type'  => 'text',
                'group' => 'seo',
            ],
            [
                'key'   => 'head_code',
                'value' => '',
                'label' => '추가 Head 코드 (meta 태그 직접 입력)',
                'type'  => 'textarea',
                'group' => 'seo',
            ],
        ];

        foreach ($extras as $item) {
            if (!DB::table('settings')->where('key', $item['key'])->exists()) {
                $item['created_at'] = now();
                $item['updated_at'] = now();
                DB::table('settings')->insert($item);
            }
        }
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('og_image')->nullable();
            $table->boolean('noindex')->default(false);
        });

        DB::table('settings')->whereIn('key', ['meta_keywords', 'head_code'])->delete();
    }
};
