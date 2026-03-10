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

    /** 태그 이름 배열 → Tag 레코드 배열 (없으면 생성) */
    public static function syncFromNames(array $names): array
    {
        $ids = [];
        foreach ($names as $name) {
            $name = trim($name);
            if ($name === '') continue;
            $tag = static::firstOrCreate(
                ['slug' => Str::slug($name, '-')],
                ['name' => $name]
            );
            $ids[] = $tag->id;
        }
        return $ids;
    }
}
