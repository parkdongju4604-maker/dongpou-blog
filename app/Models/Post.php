<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'excerpt', 'content',
        'thumbnail', 'category', 'published', 'published_at',
    ];

    protected $casts = [
        'published'    => 'boolean',
        'published_at' => 'datetime',
    ];

    public static function boot(): void
    {
        parent::boot();
        static::creating(function (Post $post) {
            if (empty($post->slug)) {
                $post->slug = static::makeSlug($post->title);
            }
            if ($post->published && !$post->published_at) {
                $post->published_at = now();
            }
        });
    }

    /**
     * 한국어를 포함한 유니코드 슬러그 생성
     * 예: "AI 시대, 콘텐츠 마케팅" → "ai-시대-콘텐츠-마케팅"
     */
    public static function makeSlug(string $title): string
    {
        // 1. 소문자 변환
        $slug = mb_strtolower($title, 'UTF-8');
        // 2. 한글/영문/숫자/하이픈만 남기고 나머지 → 하이픈
        $slug = preg_replace('/[^\p{Hangul}\p{L}\p{N}\-]/u', '-', $slug);
        // 3. 연속 하이픈 → 단일 하이픈
        $slug = preg_replace('/-{2,}/', '-', $slug);
        // 4. 앞뒤 하이픈 제거
        $slug = trim($slug, '-');

        if (empty($slug)) {
            return substr(md5($title), 0, 12);
        }

        // 5. 중복 슬러그 처리
        $original = $slug;
        $i = 1;
        while (static::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $i++;
        }

        return $slug;
    }

    public function scopePublished($query)
    {
        return $query->where('published', true)->orderByDesc('published_at');
    }

    /**
     * 마크다운 → HTML 렌더링 (GFM 지원)
     */
    public function getRenderedContentAttribute(): string
    {
        $env = new Environment([
            'html_input'         => 'escape',   // 원시 HTML 태그 이스케이프 (XSS 방지)
            'allow_unsafe_links' => false,
        ]);
        $env->addExtension(new CommonMarkCoreExtension());
        $env->addExtension(new GithubFlavoredMarkdownExtension());

        return (new MarkdownConverter($env))->convert($this->content)->getContent();
    }

    /**
     * 읽기 시간 (한국어 기준: 분당 약 500자)
     */
    public function getReadingTimeAttribute(): int
    {
        $charCount = mb_strlen(strip_tags($this->content), 'UTF-8');
        return max(1, (int) ceil($charCount / 500));
    }
}
