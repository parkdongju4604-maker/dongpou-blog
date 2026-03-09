<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    // 블로그 홈 (공개 글 목록)
    public function index()
    {
        $posts = Post::published()->paginate(9);
        $categories = Post::published()->select('category')->distinct()->pluck('category');
        return view('posts.index', compact('posts', 'categories'));
    }

    // 카테고리 필터
    public function category(string $category)
    {
        $posts = Post::published()->where('category', $category)->paginate(9);
        $categories = Post::published()->select('category')->distinct()->pluck('category');
        return view('posts.index', compact('posts', 'categories', 'category'));
    }

    // 글 상세
    public function show(string $slug)
    {
        $post = Post::where('slug', $slug)->where('published', true)->firstOrFail();
        $related = Post::published()
            ->where('category', $post->category)
            ->where('id', '!=', $post->id)
            ->limit(3)->get();
        return view('posts.show', compact('post', 'related'));
    }

    // --- 관리자 ---

    // 전체 글 목록 (관리자)
    public function adminIndex()
    {
        $posts = Post::orderByDesc('created_at')->paginate(20);
        return view('admin.posts.index', compact('posts'));
    }

    // 글 작성 폼
    public function create()
    {
        return view('admin.posts.create');
    }

    // 글 저장
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'    => 'required|max:255',
            'content'  => 'required',
            'excerpt'  => 'nullable|max:500',
            'category' => 'required|max:100',
            'published'=> 'boolean',
        ]);

        $data['slug']      = Str::slug($data['title']) ?: Str::random(8);
        $data['published'] = $request->boolean('published');
        if ($data['published']) {
            $data['published_at'] = now();
        }

        Post::create($data);
        return redirect()->route('admin.posts.index')->with('success', '글이 등록되었습니다.');
    }

    // 글 수정 폼
    public function edit(Post $post)
    {
        return view('admin.posts.edit', compact('post'));
    }

    // 글 업데이트
    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title'    => 'required|max:255',
            'content'  => 'required',
            'excerpt'  => 'nullable|max:500',
            'category' => 'required|max:100',
            'published'=> 'boolean',
        ]);

        $data['published'] = $request->boolean('published');
        if ($data['published'] && !$post->published_at) {
            $data['published_at'] = now();
        }

        $post->update($data);
        return redirect()->route('admin.posts.index')->with('success', '수정되었습니다.');
    }

    // 글 삭제
    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', '삭제되었습니다.');
    }
}
