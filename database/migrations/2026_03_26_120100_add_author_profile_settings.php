<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $extras = [
            ['key' => 'author_nickname', 'value' => '', 'label' => '블로그 운영자 닉네임', 'type' => 'text', 'group' => 'seo'],
            ['key' => 'author_description', 'value' => '', 'label' => '운영자 설명 (JSON-LD)', 'type' => 'textarea', 'group' => 'seo'],
            ['key' => 'author_slug', 'value' => '', 'label' => '작성자 아카이브 슬러그', 'type' => 'text', 'group' => 'seo'],
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
        DB::table('settings')
            ->whereIn('key', ['author_nickname', 'author_description', 'author_slug'])
            ->delete();
    }
};
