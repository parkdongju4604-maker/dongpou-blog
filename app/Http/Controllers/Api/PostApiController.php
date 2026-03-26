<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiToken;
use App\Models\Category;
use App\Models\Post;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostApiController extends Controller
{
    /**
     * GET /api/posts
     *
     * Query:
     * - status: publish|draft|future|any (default: publish)
     * - per_page: 1~50 (default: 10)
     * - page: 1..n (default: 1)
     * - orderby: date|modified|id (default: date)
     * - order: asc|desc (default: desc)
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'status'   => 'nullable|in:publish,draft,future,any',
            'per_page' => 'nullable|integer|min:1|max:50',
            'page'     => 'nullable|integer|min:1',
            'orderby'  => 'nullable|in:date,modified,id',
            'order'    => 'nullable|in:asc,desc',
        ]);

        $requestedStatus = $validated['status'] ?? 'publish';
        $perPage = (int) ($validated['per_page'] ?? 10);
        $page    = (int) ($validated['page'] ?? 1);
        $orderby = $validated['orderby'] ?? 'date';
        $order   = $validated['order'] ?? 'desc';
        $isAuthenticated = $this->hasValidBearerToken($request);
        $status = $isAuthenticated ? $requestedStatus : 'publish';

        $query = Post::query()->with(['tags:id,slug']);

        match ($status) {
            'publish' => $query->where('published', true)->where('published_at', '<=', now()),
            'draft'   => $query->where('published', false),
            'future'  => $query->where('published', true)->where('published_at', '>', now()),
            default   => $query, // any
        };

        match ($orderby) {
            'modified' => $query->orderBy('updated_at', $order),
            'id'       => $query->orderBy('id', $order),
            default    => $query->orderByRaw("COALESCE(published_at, created_at) {$order}"),
        };

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);
        $posts = $paginator->getCollection();

        $categoryByName = Category::whereIn('name', $posts->pluck('category')->filter()->unique()->values())
            ->get(['id', 'name', 'slug'])
            ->keyBy('name');

        $payload = $posts->map(function (Post $post) use ($categoryByName) {
            $wpStatus = $this->toWpStatus($post);
            $date = $post->published_at ?? $post->created_at;
            $excerptText = $post->excerpt ?: Str::limit(strip_tags($post->rendered_content), 180);

            $category = $categoryByName->get($post->category);
            $categoryIds = $category ? [$category->id] : [];
            $categoryClass = $category ? ['category-' . $category->slug] : [];

            $tagIds = $post->tags->pluck('id')->values()->all();
            $tagClasses = $post->tags->pluck('slug')->map(fn($slug) => 'tag-' . $slug)->values()->all();

            return [
                'id'            => $post->id,
                'date'          => $date?->format('Y-m-d\TH:i:s'),
                'date_gmt'      => $date?->copy()->setTimezone('UTC')->format('Y-m-d\TH:i:s'),
                'guid'          => ['rendered' => url('/?p=' . $post->id)],
                'modified'      => $post->updated_at?->format('Y-m-d\TH:i:s'),
                'modified_gmt'  => $post->updated_at?->copy()->setTimezone('UTC')->format('Y-m-d\TH:i:s'),
                'slug'          => rawurlencode($post->slug),
                'status'        => $wpStatus,
                'type'          => 'post',
                'link'          => route('posts.show', ['categorySlug' => $post->category_path_segment, 'slug' => $post->slug]),
                'title'         => ['rendered' => e($post->title)],
                'content'       => [
                    'rendered'  => $post->rendered_content,
                    'protected' => false,
                ],
                'excerpt'       => [
                    'rendered'  => '<p>' . e($excerptText) . '</p>',
                    'protected' => false,
                ],
                'author'        => 1,
                'featured_media'=> 0,
                'comment_status'=> 'open',
                'ping_status'   => 'open',
                'sticky'        => false,
                'template'      => '',
                'format'        => 'standard',
                'meta'          => ['footnotes' => ''],
                'categories'    => $categoryIds,
                'tags'          => $tagIds,
                'class_list'    => array_values(array_merge([
                    'post-' . $post->id,
                    'post',
                    'type-post',
                    'status-' . $wpStatus,
                    'format-standard',
                    'hentry',
                ], $categoryClass, $tagClasses)),
                '_links'        => [
                    'self'       => [['href' => url('/api/posts/' . $post->id), 'targetHints' => ['allow' => ['GET']]]],
                    'collection' => [['href' => url('/api/posts')]],
                    'about'      => [['href' => url('/api/types/post')]],
                ],
            ];
        })->values();

        return response()->json(
            $payload,
            200,
            [
                'X-WP-Total'      => (string) $paginator->total(),
                'X-WP-TotalPages' => (string) $paginator->lastPage(),
            ]
        );
    }

    /**
     * POST /api/posts
     *
     * Body (JSON):
     * {
     *   "title":        "글 제목",          // required
     *   "content":      "## 마크다운 또는 <p>HTML</p>", // required
     *   "content_type": "markdown|html",   // optional (default: markdown)
     *   "excerpt":      "요약",             // optional
     *   "category":     "개발",             // required
     *   "author_name":  "피부관리사 OOO",   // optional
     *   "tags":         "태그1,태그2",      // optional (comma-separated)
     *   "publish_type": "publish|draft|schedule", // default: publish
     *   "scheduled_at": "2026-03-10T09:00" // required if publish_type=schedule
     * }
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'content_type' => 'nullable|in:markdown,html',
            'excerpt'      => 'nullable|string|max:500',
            'category'     => 'required|string|max:100',
            'author_name'  => 'nullable|string|max:120',
            'tags'         => 'nullable|string|max:500',
            'publish_type' => 'nullable|in:publish,draft,schedule',
            'scheduled_at' => 'nullable|date|after:now',
        ], [
            'title.required'          => 'title은 필수입니다.',
            'content.required'        => 'content는 필수입니다.',
            'category.required'       => 'category는 필수입니다.',
            'content_type.in'         => 'content_type은 markdown 또는 html이어야 합니다.',
            'publish_type.in'         => 'publish_type은 publish, draft, schedule 중 하나여야 합니다.',
            'scheduled_at.after'      => 'scheduled_at은 현재 시간 이후여야 합니다.',
        ]);

        $publishType = $validated['publish_type'] ?? 'publish';
        $contentType = $validated['content_type'] ?? 'markdown';
        $authorName = $this->resolvePostAuthorName($validated['author_name'] ?? null);

        [$published, $publishedAt] = match ($publishType) {
            'publish'  => [true,  now()],
            'schedule' => [true,  Carbon::parse($validated['scheduled_at'])],
            default    => [false, null],  // draft
        };

        if ($publishType === 'schedule' && empty($validated['scheduled_at'])) {
            return response()->json([
                'error'   => 'Validation Error',
                'message' => 'publish_type이 schedule일 때 scheduled_at은 필수입니다.',
            ], 422);
        }

        $thumbnail = Post::extractFirstImage($validated['content']);

        $post = Post::create([
            'title'        => $validated['title'],
            'slug'         => Post::makeSlug($validated['title']),
            'content'      => $validated['content'],
            'content_type' => $contentType,
            'excerpt'      => $validated['excerpt'] ?? null,
            'category'     => $validated['category'],
            'author_name'  => $authorName,
            'published'    => $published,
            'published_at' => $publishedAt,
            'thumbnail'    => $thumbnail,
        ]);

        // 태그 추가
        if (!empty($validated['tags'])) {
            $tagNames = array_filter(array_map('trim', explode(',', $validated['tags'])));
            $tagIds   = \App\Models\Tag::syncFromNames($tagNames);
            $post->tags()->sync($tagIds);
        }

        return response()->json([
            'data' => [
                'id'           => $post->id,
                'title'        => $post->title,
                'slug'         => $post->slug,
                'url'          => route('posts.show', ['categorySlug' => $post->category_path_segment, 'slug' => $post->slug]),
                'category'     => $post->category,
                'author_name'  => $post->author_name,
                'status'       => $post->status,
                'published_at' => $post->published_at?->toIso8601String(),
                'created_at'   => $post->created_at->toIso8601String(),
            ],
            'message' => '글이 성공적으로 등록되었습니다.',
        ], 201);
    }

    private function toWpStatus(Post $post): string
    {
        if (!$post->published) {
            return 'draft';
        }

        if ($post->published_at && $post->published_at->isFuture()) {
            return 'future';
        }

        return 'publish';
    }

    private function hasValidBearerToken(Request $request): bool
    {
        $bearer = $request->bearerToken();
        if (!$bearer) {
            return false;
        }

        return ApiToken::findByToken($bearer) !== null;
    }

    private function resolvePostAuthorName(?string $authorName): string
    {
        $authorName = trim((string) $authorName);
        if ($authorName !== '') {
            return $authorName;
        }

        $authorNickname = trim((string) Setting::get('author_nickname', ''));
        if ($authorNickname !== '') {
            return $authorNickname;
        }

        $defaultAuthor = trim((string) Setting::get('author_name', ''));
        if ($defaultAuthor !== '') {
            return $defaultAuthor;
        }

        $blogName = trim((string) Setting::get('blog_name', config('app.name', 'Blog')));
        return $blogName !== '' ? $blogName : (string) config('app.name', 'Blog');
    }
}
