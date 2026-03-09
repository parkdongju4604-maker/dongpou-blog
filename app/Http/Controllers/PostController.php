<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    // ── 공개 라우트 ──────────────────────────────

    public function index()
    {
        $perPage    = (int) Setting::get('posts_per_page', 9);
        $posts      = Post::published()->paginate($perPage);
        $categories = Post::where('published', true)->distinct()->pluck('category');
        return view('posts.index', compact('posts', 'categories'));
    }

    public function category(string $category)
    {
        $perPage    = (int) Setting::get('posts_per_page', 9);
        $posts      = Post::published()->where('category', $category)->paginate($perPage);
        $categories = Post::where('published', true)->distinct()->pluck('category');
        return view('posts.index', compact('posts', 'categories', 'category'));
    }

    public function show(string $slug)
    {
        $post    = Post::where('slug', $slug)->where('published', true)->firstOrFail();
        $related = Post::published()
            ->where('category', $post->category)
            ->where('id', '!=', $post->id)
            ->limit(3)->get();
        return view('posts.show', compact('post', 'related'));
    }

    // ── 관리자 라우트 ────────────────────────────

    public function dashboard()
    {
        return view('admin.dashboard', [
            'totalPosts'      => Post::count(),
            'publishedPosts'  => Post::where('published', true)->count(),
            'draftPosts'      => Post::where('published', false)->count(),
            'totalCategories' => Category::count(),
            'recentPosts'     => Post::orderByDesc('created_at')->limit(8)->get(),
            'blogName'        => Setting::get('blog_name', 'DongPou Blog'),
            'blogTagline'     => Setting::get('blog_tagline', '개인 블로그'),
        ]);
    }

    public function adminIndex()
    {
        $posts = Post::orderByDesc('created_at')->paginate(20);
        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = Category::ordered()->get();
        return view('admin.posts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'     => 'required|max:255',
            'content'   => 'required',
            'excerpt'   => 'nullable|max:500',
            'category'  => 'required|max:100',
            'published' => 'boolean',
        ]);

        $data['slug']      = Str::slug($data['title']) ?: Str::random(8);
        $data['published'] = $request->boolean('published');
        if ($data['published']) {
            $data['published_at'] = now();
        }

        Post::create($data);
        return redirect()->route('admin.posts.index')->with('success', '글이 등록되었습니다.');
    }

    public function edit(Post $post)
    {
        $categories = Category::ordered()->get();
        return view('admin.posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title'     => 'required|max:255',
            'content'   => 'required',
            'excerpt'   => 'nullable|max:500',
            'category'  => 'required|max:100',
            'published' => 'boolean',
        ]);

        $data['published'] = $request->boolean('published');
        if ($data['published'] && !$post->published_at) {
            $data['published_at'] = now();
        }

        $post->update($data);
        return redirect()->route('admin.posts.index')->with('success', '수정되었습니다.');
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', '삭제되었습니다.');
    }
}
