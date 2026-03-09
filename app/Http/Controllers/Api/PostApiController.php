<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PostApiController extends Controller
{
    /**
     * POST /api/posts
     *
     * Body (JSON):
     * {
     *   "title":        "글 제목",          // required
     *   "content":      "## 마크다운 내용", // required (markdown)
     *   "excerpt":      "요약",             // optional
     *   "category":     "개발",             // required
     *   "publish_type": "publish|draft|schedule", // default: publish
     *   "scheduled_at": "2026-03-10T09:00" // required if publish_type=schedule
     * }
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'excerpt'      => 'nullable|string|max:500',
            'category'     => 'required|string|max:100',
            'publish_type' => 'nullable|in:publish,draft,schedule',
            'scheduled_at' => 'nullable|date|after:now',
        ], [
            'title.required'          => 'title은 필수입니다.',
            'content.required'        => 'content는 필수입니다.',
            'category.required'       => 'category는 필수입니다.',
            'publish_type.in'         => 'publish_type은 publish, draft, schedule 중 하나여야 합니다.',
            'scheduled_at.after'      => 'scheduled_at은 현재 시간 이후여야 합니다.',
        ]);

        $publishType = $validated['publish_type'] ?? 'publish';

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
            'excerpt'      => $validated['excerpt'] ?? null,
            'category'     => $validated['category'],
            'published'    => $published,
            'published_at' => $publishedAt,
            'thumbnail'    => $thumbnail,
        ]);

        return response()->json([
            'data' => [
                'id'           => $post->id,
                'title'        => $post->title,
                'slug'         => $post->slug,
                'url'          => route('posts.show', $post->slug),
                'category'     => $post->category,
                'status'       => $post->status,
                'published_at' => $post->published_at?->toIso8601String(),
                'created_at'   => $post->created_at->toIso8601String(),
            ],
            'message' => '글이 성공적으로 등록되었습니다.',
        ], 201);
    }
}
