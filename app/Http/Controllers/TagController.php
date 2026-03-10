<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Setting;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index(string $slug)
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();

        $perPage = (int) Setting::get('posts_per_page', 9);
        $posts   = $tag->posts()->published()->paginate($perPage);

        $categories = Post::published()->reorder()->distinct()->pluck('category');

        return view('posts.index', compact('posts', 'categories'))
            ->with('pageTitle', '#' . $tag->name)
            ->with('tag', $tag);
    }

    /** 전체 태그 목록 (JSON, 관리자 자동완성용) */
    public function all()
    {
        return response()->json(Tag::orderBy('name')->get(['id', 'name', 'slug']));
    }
}
