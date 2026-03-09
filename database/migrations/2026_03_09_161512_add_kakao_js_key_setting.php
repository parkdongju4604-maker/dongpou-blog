<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!DB::table('settings')->where('key', 'kakao_js_key')->exists()) {
            DB::table('settings')->insert([
                'key'        => 'kakao_js_key',
                'value'      => '',
                'label'      => '카카오톡 공유 JavaScript 키',
                'type'       => 'text',
                'group'      => 'seo',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'kakao_js_key')->delete();
    }
};
