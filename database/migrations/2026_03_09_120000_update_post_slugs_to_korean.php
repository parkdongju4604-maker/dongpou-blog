<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 모든 포스트의 슬러그를 제목 기반 한국어 슬러그로 재생성
        $posts = DB::table('posts')->get();

        foreach ($posts as $post) {
            $slug = mb_strtolower($post->title, 'UTF-8');
            $slug = preg_replace('/[^\p{Hangul}\p{L}\p{N}\-]/u', '-', $slug);
            $slug = preg_replace('/-{2,}/', '-', $slug);
            $slug = trim($slug, '-');

            if (empty($slug)) {
                $slug = substr(md5($post->title), 0, 12);
            }

            // 중복 처리
            $original = $slug;
            $i = 1;
            while (DB::table('posts')->where('slug', $slug)->where('id', '!=', $post->id)->exists()) {
                $slug = $original . '-' . $i++;
            }

            DB::table('posts')->where('id', $post->id)->update(['slug' => $slug]);
        }
    }

    public function down(): void
    {
        // 롤백 불필요 (원복 데이터 없음)
    }
};
