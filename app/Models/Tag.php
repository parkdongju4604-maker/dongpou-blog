<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Tag extends Model
{
    protected $fillable = ['name', 'slug'];

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }

    /** 태그명 → slug 변환 (영문 slug 우선, 한글/유니코드 허용) */
    public static function makeSlug(string $name): string
    {
        $normalized = trim((string) preg_replace('/\s+/u', ' ', $name));

        $asciiSlug = Str::slug($normalized, '-');
        if ($asciiSlug !== '') {
            return $asciiSlug;
        }

        // 한글/유니코드 문자와 숫자는 유지하고, 공백은 하이픈으로 정규화
        $unicodeSlug = (string) preg_replace('/[^\p{L}\p{N}\s-]+/u', '', $normalized);
        $unicodeSlug = (string) preg_replace('/[\s_-]+/u', '-', trim($unicodeSlug));
        $unicodeSlug = trim($unicodeSlug, '-');

        return $unicodeSlug !== '' ? $unicodeSlug : 'tag-' . substr(md5($normalized), 0, 8);
    }

    /** 태그 이름 배열 → Tag 레코드 배열 (없으면 생성, 기존 slug 자동 보정) */
    public static function syncFromNames(array $names): array
    {
        $ids = [];
        foreach ($names as $name) {
            $name = trim($name);
            if ($name === '') {
                continue;
            }

            $slug = static::makeSlug($name);
            $tag = static::query()->where('name', $name)->first();

            if ($tag) {
                $desiredSlug = static::ensureUniqueSlug($slug, $tag->id);
                if ($tag->slug !== $desiredSlug) {
                    $tag->update(['slug' => $desiredSlug]);
                }
            } else {
                $tag = static::create([
                    'name' => $name,
                    'slug' => static::ensureUniqueSlug($slug),
                ]);
            }

            $ids[] = $tag->id;
        }
        return $ids;
    }

    private static function ensureUniqueSlug(string $baseSlug, ?int $ignoreId = null): string
    {
        $baseSlug = trim($baseSlug, '-');
        if ($baseSlug === '') {
            $baseSlug = 'tag';
        }

        $maxBaseLength = 60;
        $candidate = mb_substr($baseSlug, 0, $maxBaseLength);
        $suffix = 2;

        while (true) {
            $query = static::query()->where('slug', $candidate);
            if ($ignoreId !== null) {
                $query->where('id', '!=', $ignoreId);
            }

            if (!$query->exists()) {
                return $candidate;
            }

            $suffixText = '-' . $suffix++;
            $trimmedBase = mb_substr($baseSlug, 0, 60 - mb_strlen($suffixText));
            $candidate = $trimmedBase . $suffixText;
        }
    }
}
