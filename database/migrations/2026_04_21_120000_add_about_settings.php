<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $items = [
            ['key' => 'about_enabled', 'value' => '0', 'label' => 'About 페이지 사용', 'type' => 'text', 'group' => 'general'],
            ['key' => 'about_title', 'value' => 'About', 'label' => 'About 페이지 제목', 'type' => 'text', 'group' => 'general'],
            ['key' => 'about_html', 'value' => '', 'label' => 'About 페이지 HTML', 'type' => 'textarea', 'group' => 'general'],
        ];

        foreach ($items as $item) {
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
            ->whereIn('key', ['about_enabled', 'about_title', 'about_html'])
            ->delete();
    }
};
