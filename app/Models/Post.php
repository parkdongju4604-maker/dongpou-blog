<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'excerpt', 'content', 'content_type',
        'thumbnail', 'category', 'published', 'published_at',
        'view_count',
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
        return $query
            ->where('published', true)
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at');
    }

    /**
     * 포스트 상태: draft / scheduled / published
     */
    public function getStatusAttribute(): string
    {
        if (!$this->published) return 'draft';
        if ($this->published_at && $this->published_at->isFuture()) return 'scheduled';
        return 'published';
    }

    /**
     * 콘텐츠 렌더링
     * - content_type = 'html'  : 저장된 HTML 그대로 출력
     * - content_type = 'markdown' (기본) : CommonMark(GFM)로 변환
     */
    public function getRenderedContentAttribute(): string
    {
        // ── HTML 모드: 그대로 반환 ──────────────────────────────
        if (($this->content_type ?? 'markdown') === 'html') {
            return $this->content ?? '';
        }

        // ── 마크다운 모드 ───────────────────────────────────────
        $content = $this->content;
        $replacements = [];

        // 1) Toast UI 너비 문법 `![alt](url =NNNx)` → placeholder로 교체
        $content = preg_replace_callback(
            '/!\[([^\]]*)\]\(([^ \)\n]+) =(\d+)x\)/',
            function ($m) use (&$replacements) {
                $key = '__IMG_' . count($replacements) . '__';
                $replacements[$key] = sprintf(
                    '<img src="%s" alt="%s" style="max-width:%dpx;width:100%%;height:auto;'
                    . 'border-radius:12px;margin:1.5rem auto;display:block">',
                    htmlspecialchars($m[2], ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars($m[1], ENT_QUOTES, 'UTF-8'),
                    (int) $m[3]
                );
                return $key;
            },
            $content
        );

        // 2) CommonMark 렌더링
        $env = new Environment(['html_input' => 'escape', 'allow_unsafe_links' => false]);
        $env->addExtension(new CommonMarkCoreExtension());
        $env->addExtension(new GithubFlavoredMarkdownExtension());
        $html = (new MarkdownConverter($env))->convert($content)->getContent();

        // 3) placeholder → 실제 <img> 태그로 복원
        foreach ($replacements as $key => $imgTag) {
            $html = str_replace(htmlspecialchars($key), $imgTag, $html);
            $html = str_replace($key, $imgTag, $html);
        }

        return $html;
    }

    /**
     * 마크다운 콘텐츠에서 첫 번째 이미지 URL 추출
     * 지원 형식: ![alt](url) / ![alt](url =Nx) / <img src="url">
     */
    public static function extractFirstImage(string $content): ?string
    {
        // 마크다운 이미지: ![alt](url) 또는 ![alt](url =Nx)
        if (preg_match('/!\[[^\]]*\]\(([^\s\)]+)(?:\s*=[^\)]*)?\)/', $content, $m)) {
            return $m[1];
        }
        // HTML img 태그 (직접 입력한 경우)
        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/', $content, $m)) {
            return $m[1];
        }
        return null;
    }

    /**
     * 읽기 시간 (한국어 기준: 분당 약 500자)
     */
    public function getReadingTimeAttribute(): int
    {
        $charCount = mb_strlen(strip_tags($this->content), 'UTF-8');
        return max(1, (int) ceil($charCount / 500));
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }
}
