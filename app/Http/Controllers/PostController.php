<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // ── 공개 라우트 ──────────────────────────────

    public function index()
    {
        $perPage    = (int) Setting::get('posts_per_page', 9);
        $posts      = Post::published()->paginate($perPage);
        $categories = Post::published()->reorder()->distinct()->pluck('category');
        return view('posts.index', compact('posts', 'categories'));
    }

    public function category(string $category)
    {
        $perPage    = (int) Setting::get('posts_per_page', 9);
        $posts      = Post::published()->where('category', $category)->paginate($perPage);
        $categories = Post::published()->reorder()->distinct()->pluck('category');
        return view('posts.index', compact('posts', 'categories', 'category'));
    }

    public function show(string $slug)
    {
        $post    = Post::published()->where('slug', $slug)->firstOrFail();
        $post->increment('view_count');
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
            'publishedPosts'  => Post::published()->count(),
            'scheduledPosts'  => Post::where('published', true)->where('published_at', '>', now())->count(),
            'draftPosts'      => Post::where('published', false)->count(),
            'totalCategories' => Category::count(),
            'recentPosts'     => Post::orderByDesc('created_at')->limit(8)->get(),
            'blogName'        => Setting::get('blog_name', 'DongPou Blog'),
            'blogTagline'     => Setting::get('blog_tagline', '개인 블로그'),
        ]);
    }

    public function adminIndex(Request $request)
    {
        $q        = trim($request->get('q', ''));
        $status   = $request->get('status', '');
        $category = $request->get('category', '');

        $query = Post::orderByDesc('created_at');

        // 키워드 검색
        if ($q) {
            $query->where(function ($qb) use ($q) {
                $qb->where('title',    'LIKE', "%{$q}%")
                   ->orWhere('excerpt', 'LIKE', "%{$q}%");
            });
        }

        // 상태 필터
        if ($status === 'published') {
            $query->where('published', true)->where('published_at', '<=', now());
        } elseif ($status === 'scheduled') {
            $query->where('published', true)->where('published_at', '>', now());
        } elseif ($status === 'draft') {
            $query->where('published', false);
        }

        // 카테고리 필터
        if ($category) {
            $query->where('category', $category);
        }

        $posts      = $query->paginate(20)->withQueryString();
        $categories = Post::distinct()->pluck('category');

        return view('admin.posts.index', compact('posts', 'q', 'status', 'category', 'categories'));
    }

    public function create()
    {
        $categories = Category::ordered()->get();
        return view('admin.posts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'        => 'required|max:255',
            'content'      => 'required',
            'excerpt'      => 'nullable|max:500',
            'category'     => 'required|max:100',
            'publish_type' => 'required|in:draft,publish,schedule',
            'scheduled_at' => 'required_if:publish_type,schedule|nullable|date|after:now',
        ], [
            'scheduled_at.required_if' => '예약 발행 날짜/시간을 입력해주세요.',
            'scheduled_at.after'       => '예약 시간은 현재 시간 이후여야 합니다.',
        ]);

        [$published, $publishedAt] = $this->resolvePublishState($request);

        // 본문 첫 이미지를 썸네일로 자동 추출
        $thumbnail = Post::extractFirstImage($request->content);

        Post::create([
            'title'        => $request->title,
            'slug'         => Post::makeSlug($request->title),
            'content'      => $request->content,
            'excerpt'      => $request->excerpt,
            'category'     => $request->category,
            'published'    => $published,
            'published_at' => $publishedAt,
            'thumbnail'    => $thumbnail,
        ]);

        return redirect()->route('admin.posts.index')->with('success', '글이 등록되었습니다.');
    }

    public function edit(Post $post)
    {
        $categories = Category::ordered()->get();
        return view('admin.posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title'        => 'required|max:255',
            'content'      => 'required',
            'excerpt'      => 'nullable|max:500',
            'category'     => 'required|max:100',
            'publish_type' => 'required|in:draft,publish,schedule',
            'scheduled_at' => 'required_if:publish_type,schedule|nullable|date|after:now',
        ], [
            'scheduled_at.required_if' => '예약 발행 날짜/시간을 입력해주세요.',
            'scheduled_at.after'       => '예약 시간은 현재 시간 이후여야 합니다.',
        ]);

        [$published, $publishedAt] = $this->resolvePublishState($request);

        // 본문 첫 이미지를 썸네일로 자동 추출
        $thumbnail = Post::extractFirstImage($request->content);

        $post->update([
            'title'        => $request->title,
            'content'      => $request->content,
            'excerpt'      => $request->excerpt,
            'category'     => $request->category,
            'published'    => $published,
            'published_at' => $publishedAt,
            'thumbnail'    => $thumbnail,
        ]);

        return redirect()->route('admin.posts.index')->with('success', '수정되었습니다.');
    }

    /**
     * publish_type → (published, published_at) 변환
     */
    private function resolvePublishState(Request $request): array
    {
        return match ($request->publish_type) {
            'publish'  => [true,  now()],
            'schedule' => [true,  Carbon::parse($request->scheduled_at)],
            default    => [false, null],  // draft
        };
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', '삭제되었습니다.');
    }
}
