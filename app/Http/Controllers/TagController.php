<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Setting;
use App\Models\Tag;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function index(string $slug)
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();

        $perPage = (int) Setting::get('posts_per_page', 9);
        $posts   = $tag->posts()->published()->paginate($perPage);

        $categories = $this->buildCategoryTabs();

        return view('posts.index', compact('posts', 'categories'))
            ->with('pageTitle', '#' . $tag->name)
            ->with('tag', $tag);
    }

    /** 전체 태그 목록 (JSON, 관리자 자동완성용) */
    public function all()
    {
        return response()->json(Tag::orderBy('name')->get(['id', 'name', 'slug']));
    }

    private function buildCategoryTabs()
    {
        $names = Post::published()->reorder()->distinct()->pluck('category');

        return $names->map(function (string $name) {
            return [
                'name' => $name,
                'slug' => $this->resolveCategoryPathSegment($name),
            ];
        });
    }

    private function resolveCategoryPathSegment(string $categoryName): string
    {
        $slug = Category::query()->where('name', $categoryName)->value('slug');
        if (filled($slug)) {
            return (string) $slug;
        }

        $slug = Str::slug($categoryName);
        if (filled($slug)) {
            return $slug;
        }

        return rawurlencode($categoryName);
    }
}
