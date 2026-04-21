<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!DB::table('settings')->where('key', 'about_card_html')->exists()) {
            DB::table('settings')->insert([
                'key' => 'about_card_html',
                'value' => '',
                'label' => 'About 소개 카드 HTML',
                'type' => 'textarea',
                'group' => 'general',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'about_card_html')->delete();
    }
};
