<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('posts', 'author_name')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->string('author_name', 120)->nullable()->after('category');
            });
        }

        $defaultAuthor = $this->resolveDefaultAuthorName();

        DB::table('posts')
            ->where(function ($query) {
                $query->whereNull('author_name')
                    ->orWhere('author_name', '');
            })
            ->update(['author_name' => $defaultAuthor]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('posts', 'author_name')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropColumn('author_name');
            });
        }
    }

    private function resolveDefaultAuthorName(): string
    {
        $authorNickname = trim((string) DB::table('settings')->where('key', 'author_nickname')->value('value'));
        if ($authorNickname !== '') {
            return $authorNickname;
        }

        $authorName = trim((string) DB::table('settings')->where('key', 'author_name')->value('value'));
        if ($authorName !== '') {
            return $authorName;
        }

        $blogName = trim((string) DB::table('settings')->where('key', 'blog_name')->value('value'));
        if ($blogName !== '') {
            return $blogName;
        }

        return (string) config('app.name', 'Blog');
    }
};
