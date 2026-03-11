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

    /** 태그명 → slug 변환 (한글 등 비ASCII는 hash 폴백) */
    public static function makeSlug(string $name): string
    {
        $slug = Str::slug($name, '-');
        return $slug !== '' ? $slug : 'tag-' . substr(md5($name), 0, 8);
    }

    /** 태그 이름 배열 → Tag 레코드 배열 (없으면 생성) */
    public static function syncFromNames(array $names): array
    {
        $ids = [];
        foreach ($names as $name) {
            $name = trim($name);
            if ($name === '') continue;
            $slug = static::makeSlug($name);
            $tag  = static::firstOrCreate(
                ['slug' => $slug],
                ['name' => $name]
            );
            $ids[] = $tag->id;
        }
        return $ids;
    }
}
