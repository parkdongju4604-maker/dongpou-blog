<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Setting;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    // ── 공개 라우트 ──────────────────────────────

    public function index()
    {
        $perPage    = (int) Setting::get('posts_per_page', 9);
        $posts      = Post::with('tags')->published()->paginate($perPage);
        $categories = $this->buildCategoryTabs();
        return view('posts.index', compact('posts', 'categories'));
    }

    public function category(string $category)
    {
        $segment = $this->resolveCategoryPathSegment(urldecode($category));
        return redirect()->route('posts.category', ['categorySlug' => $segment], 301);
    }

    public function categoryBySlug(string $categorySlug)
    {
        [$categoryName, $canonicalSegment] = $this->resolveCategoryBySegment($categorySlug);
        if ($categorySlug !== $canonicalSegment) {
            return redirect()->route('posts.category', ['categorySlug' => $canonicalSegment], 301);
        }

        $perPage    = (int) Setting::get('posts_per_page', 9);
        $posts      = Post::with('tags')->published()->where('category', $categoryName)->paginate($perPage);
        $categories = $this->buildCategoryTabs();
        $category   = $categoryName;
        $currentCategorySlug = $canonicalSegment;

        return view('posts.index', compact('posts', 'categories', 'category', 'currentCategorySlug'));
    }

    public function authorBySlug(string $authorSlug)
    {
        [$authorName, $canonicalSlug] = $this->resolveAuthorBySlug($authorSlug);
        if ($authorSlug !== $canonicalSlug) {
            return redirect()->route('posts.author', ['authorSlug' => $canonicalSlug], 301);
        }

        $perPage = (int) Setting::get('posts_per_page', 9);
        $posts = Post::with('tags')
            ->published()
            ->where(function ($query) use ($authorName) {
                $query->where('author_name', $authorName);

                // 작성자 컬럼 도입 이전 레거시 데이터 호환
                if ($authorName === $this->defaultAuthorName()) {
                    $query->orWhereNull('author_name')->orWhere('author_name', '');
                }
            })
            ->paginate($perPage);

        $categories = $this->buildCategoryTabs();
        $authorNameForArchive = $authorName;
        $currentAuthorSlug = $canonicalSlug;

        return view('posts.index', compact('posts', 'categories', 'authorNameForArchive', 'currentAuthorSlug'));
    }

    public function show(string $slug)
    {
        $post = Post::published()->where('slug', $slug)->firstOrFail();
        return redirect()->route('posts.show', [
            'categorySlug' => $post->category_path_segment,
            'slug' => $post->slug,
        ], 301);
    }

    public function showByCategory(string $categorySlug, string $slug)
    {
        $post = Post::with('tags')->published()->where('slug', $slug)->firstOrFail();
        $canonicalSegment = $post->category_path_segment;
        if ($categorySlug !== $canonicalSegment) {
            return redirect()->route('posts.show', [
                'categorySlug' => $canonicalSegment,
                'slug' => $post->slug,
            ], 301);
        }

        return $this->renderShow($post);
    }

    private function renderShow(Post $post)
    {
        $post->increment('view_count');

        // 같은 카테고리 관련 글 (최대 3개)
        $related = Post::published()
            ->where('category', $post->category)
            ->where('id', '!=', $post->id)
            ->limit(3)->get();

        // 부족하면 최신 글로 보충
        if ($related->count() < 3) {
            $excludeIds = $related->pluck('id')->push($post->id);
            $extra = Post::published()
                ->whereNotIn('id', $excludeIds)
                ->limit(3 - $related->count())
                ->get();
            $related = $related->merge($extra);
        }

        // 이전 글 (더 오래된 글)
        $prevPost = Post::published()
            ->where('published_at', '<', $post->published_at)
            ->reorder()->orderByDesc('published_at')
            ->first(['id', 'title', 'slug', 'category']);

        // 다음 글 (더 최신 글)
        $nextPost = Post::published()
            ->where('published_at', '>', $post->published_at)
            ->reorder()->orderBy('published_at')
            ->first(['id', 'title', 'slug', 'category']);

        // 승인된 최상위 댓글 + 대댓글 eager loading
        $comments = Comment::with(['replies'])
            ->where('post_id', $post->id)
            ->visible()
            ->topLevel()
            ->orderBy('created_at')
            ->get();

        return view('posts.show', compact('post', 'related', 'prevPost', 'nextPost', 'comments'));
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

    private function resolveCategoryBySegment(string $segment): array
    {
        $decoded = urldecode($segment);

        $category = Category::query()
            ->where('slug', $segment)
            ->orWhere('name', $decoded)
            ->first();

        if ($category) {
            return [$category->name, $this->resolveCategoryPathSegment($category->name)];
        }

        return [$decoded, $this->resolveCategoryPathSegment($decoded)];
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

    private function resolveAuthorBySlug(string $slug): array
    {
        $decoded = urldecode($slug);
        $defaultAuthorName = $this->defaultAuthorName();
        $defaultAuthorSlug = $this->defaultAuthorSlug($defaultAuthorName);

        if ($slug === $defaultAuthorSlug || $decoded === $defaultAuthorName) {
            return [$defaultAuthorName, $defaultAuthorSlug];
        }

        $authorName = Post::published()
            ->reorder()
            ->whereNotNull('author_name')
            ->where('author_name', '!=', '')
            ->get(['author_name'])
            ->pluck('author_name')
            ->unique()
            ->first(function (string $name) use ($slug, $decoded) {
                return $this->slugifyAuthorName($name) === $slug || $name === $decoded;
            });

        if (!$authorName) {
            abort(404);
        }

        return [$authorName, $this->slugifyAuthorName($authorName)];
    }

    private function defaultAuthorSlug(string $defaultAuthorName): string
    {
        $configured = trim((string) Setting::get('author_slug', ''));
        if ($configured !== '') {
            return trim($configured, '/');
        }

        return $this->slugifyAuthorName($defaultAuthorName);
    }

    private function slugifyAuthorName(string $authorName): string
    {
        $slug = Str::slug($authorName);
        if (filled($slug)) {
            return $slug;
        }

        return 'author-' . substr(md5($authorName), 0, 12);
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
            'content_type' => 'nullable|in:markdown,html',
            'excerpt'      => 'nullable|max:500',
            'category'     => 'required|max:100',
            'author_name'  => 'nullable|string|max:120',
            'publish_type' => 'required|in:draft,publish,schedule',
            'scheduled_at' => 'required_if:publish_type,schedule|nullable|date|after:now',
            'tags'         => 'nullable|string|max:500',
        ], [
            'scheduled_at.required_if' => '예약 발행 날짜/시간을 입력해주세요.',
            'scheduled_at.after'       => '예약 시간은 현재 시간 이후여야 합니다.',
        ]);

        [$published, $publishedAt] = $this->resolvePublishState($request);
        $contentType = $request->input('content_type', 'markdown');
        $authorName = $this->resolvePostAuthorName($request->input('author_name'));

        // 본문 첫 이미지를 썸네일로 자동 추출
        $thumbnail = Post::extractFirstImage($request->content);

        $post = Post::create([
            'title'        => $request->title,
            'slug'         => Post::makeSlug($request->title),
            'content'      => $request->content,
            'content_type' => $contentType,
            'excerpt'      => $request->excerpt,
            'category'     => $request->category,
            'author_name'  => $authorName,
            'published'    => $published,
            'published_at' => $publishedAt,
            'thumbnail'    => $thumbnail,
        ]);

        // 태그 동기화
        $tagNames = array_filter(array_map('trim', explode(',', $request->tags ?? '')));
        $tagIds   = Tag::syncFromNames($tagNames);
        $post->tags()->sync($tagIds);

        return redirect()->route('admin.posts.index')->with('success', '글이 등록되었습니다.');
    }

    public function edit(Post $post)
    {
        $post->load('tags');
        $categories = Category::ordered()->get();
        return view('admin.posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title'        => 'required|max:255',
            'content'      => 'required',
            'content_type' => 'nullable|in:markdown,html',
            'excerpt'      => 'nullable|max:500',
            'category'     => 'required|max:100',
            'author_name'  => 'nullable|string|max:120',
            'publish_type' => 'required|in:draft,publish,schedule',
            'scheduled_at' => 'required_if:publish_type,schedule|nullable|date|after:now',
            'tags'         => 'nullable|string|max:500',
        ], [
            'scheduled_at.required_if' => '예약 발행 날짜/시간을 입력해주세요.',
            'scheduled_at.after'       => '예약 시간은 현재 시간 이후여야 합니다.',
        ]);

        [$published, $publishedAt] = $this->resolvePublishState($request);
        $contentType = $request->input('content_type', 'markdown');
        $authorName = $this->resolvePostAuthorName($request->input('author_name'));

        // 본문 첫 이미지를 썸네일로 자동 추출
        $thumbnail = Post::extractFirstImage($request->content);

        $post->update([
            'title'        => $request->title,
            'content'      => $request->content,
            'content_type' => $contentType,
            'excerpt'      => $request->excerpt,
            'category'     => $request->category,
            'author_name'  => $authorName,
            'published'    => $published,
            'published_at' => $publishedAt,
            'thumbnail'    => $thumbnail,
        ]);

        // 태그 동기화
        $tagNames = array_filter(array_map('trim', explode(',', $request->tags ?? '')));
        $tagIds   = Tag::syncFromNames($tagNames);
        $post->tags()->sync($tagIds);

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

    private function resolvePostAuthorName(?string $authorName): string
    {
        $authorName = trim((string) $authorName);
        if ($authorName !== '') {
            return $authorName;
        }

        return $this->defaultAuthorName();
    }

    private function defaultAuthorName(): string
    {
        $authorNickname = trim((string) Setting::get('author_nickname', ''));
        if ($authorNickname !== '') {
            return $authorNickname;
        }

        $authorName = trim((string) Setting::get('author_name', ''));
        if ($authorName !== '') {
            return $authorName;
        }

        $blogName = trim((string) Setting::get('blog_name', config('app.name', 'Blog')));
        return $blogName !== '' ? $blogName : (string) config('app.name', 'Blog');
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', '삭제되었습니다.');
    }

    /**
     * 미리보기 (마크다운/HTML)
     */
    public function preview(Request $request)
    {
        $request->validate([
            'content'      => 'required',
            'content_type' => 'in:markdown,html',
        ]);

        $content = $request->content;
        $contentType = $request->input('content_type', 'markdown');

        if ($contentType === 'markdown') {
            $parsedown = new \Parsedown();
            $html = $parsedown->text($content);
        } else {
            $html = $content;
        }

        $themeCss = \App\Models\CssTheme::getActive()?->css ?? '';

        return response()->json([
            'html'      => $html,
            'theme_css' => $themeCss,
        ]);
    }
}
