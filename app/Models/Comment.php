<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;

class Comment extends Model
{
    protected $fillable = [
        'post_id', 'parent_id', 'author_name', 'author_email',
        'password_hash', 'content', 'ip_hash',
        'is_approved', 'is_spam', 'spam_score',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'is_spam'     => 'boolean',
    ];

    // ── 관계 ──────────────────────────────────────

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')
                    ->where('is_approved', true)
                    ->where('is_spam', false)
                    ->orderBy('created_at');
    }

    // ── 스코프 ────────────────────────────────────

    public function scopeVisible($query)
    {
        return $query->where('is_approved', true)->where('is_spam', false);
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    // ── 헬퍼 ──────────────────────────────────────

    public function checkPassword(string $password): bool
    {
        if (!$this->password_hash) return false;
        return Hash::check($password, $this->password_hash);
    }

    /**
     * 스팸 점수 계산
     * 10점 이상이면 스팸 처리
     */
    public static function calcSpamScore(
        string $content,
        string $honeypot,
        string $ipHash,
        ?int   $postId = null
    ): int {
        $score = 0;

        // 1. 허니팟 (봇이 채우는 숨김 필드)
        if ($honeypot !== '') {
            $score += 10;
        }

        // 2. 본문 URL 과다
        $urlCount = preg_match_all('/https?:\/\//i', $content);
        if ($urlCount >= 2) $score += 4;
        if ($urlCount >= 4) $score += 4;

        // 3. 10분 내 동일 IP 댓글 3개 이상
        $recentCount = static::where('ip_hash', $ipHash)
            ->where('created_at', '>=', now()->subMinutes(10))
            ->count();
        if ($recentCount >= 3) $score += 6;
        if ($recentCount >= 6) $score += 10;

        // 4. 같은 IP + 같은 내용 중복
        $duplicate = static::where('ip_hash', $ipHash)
            ->where('content', $content)
            ->where('created_at', '>=', now()->subDay())
            ->exists();
        if ($duplicate) $score += 10;

        // 5. 내용이 너무 짧거나 의심스러운 패턴
        if (mb_strlen($content) < 3) $score += 5;

        return $score;
    }
}
