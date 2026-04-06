<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $tags = DB::table('tags')
            ->select('id', 'name', 'slug')
            ->orderBy('id')
            ->get();

        $taken = [];
        foreach ($tags as $tag) {
            $taken[$tag->slug] = true;
        }

        foreach ($tags as $tag) {
            unset($taken[$tag->slug]);

            $desired = $this->ensureUniqueSlug(
                $this->makeReadableSlug($tag->name),
                $taken
            );

            if ($desired !== $tag->slug) {
                DB::table('tags')
                    ->where('id', $tag->id)
                    ->update(['slug' => $desired]);
            }

            $taken[$desired] = true;
        }
    }

    public function down(): void
    {
        // no-op
    }

    private function makeReadableSlug(string $name): string
    {
        $normalized = trim((string) preg_replace('/\s+/u', ' ', $name));

        $asciiSlug = Str::slug($normalized, '-');
        if ($asciiSlug !== '') {
            return $asciiSlug;
        }

        $unicodeSlug = (string) preg_replace('/[^\p{L}\p{N}\s-]+/u', '', $normalized);
        $unicodeSlug = (string) preg_replace('/[\s_-]+/u', '-', trim($unicodeSlug));
        $unicodeSlug = trim($unicodeSlug, '-');

        return $unicodeSlug !== '' ? $unicodeSlug : 'tag-' . substr(md5($normalized), 0, 8);
    }

    private function ensureUniqueSlug(string $baseSlug, array $taken): string
    {
        $baseSlug = trim($baseSlug, '-');
        if ($baseSlug === '') {
            $baseSlug = 'tag';
        }

        $candidate = mb_substr($baseSlug, 0, 60);
        $suffix = 2;

        while (isset($taken[$candidate])) {
            $suffixText = '-' . $suffix++;
            $trimmedBase = mb_substr($baseSlug, 0, 60 - mb_strlen($suffixText));
            $candidate = $trimmedBase . $suffixText;
        }

        return $candidate;
    }
};
