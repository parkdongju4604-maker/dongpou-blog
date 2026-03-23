<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
//test
class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::query()
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|max:100|unique:categories,name',
            'slug'        => 'nullable|max:120|unique:categories,slug|regex:/^[a-z0-9\-]+$/',
            'description' => 'nullable|max:255',
            'sort_order'  => 'nullable|integer',
        ], [
            'slug.unique' => '이미 사용 중인 슬러그입니다.',
            'slug.regex'  => '슬러그는 영소문자, 숫자, 하이픈(-)만 사용 가능합니다.',
        ]);

        $data['slug']       = filled($data['slug'] ?? null)
            ? $data['slug']
            : (Str::slug($data['name']) ?: Str::lower(Str::random(8)));
        $data['sort_order'] = $data['sort_order'] ?? 0;

        Category::create($data);
        return redirect()->route('admin.categories.index')->with('success', '카테고리가 추가되었습니다.');
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'        => 'required|max:100|unique:categories,name,' . $category->id,
            'slug'        => 'nullable|max:120|unique:categories,slug,' . $category->id . '|regex:/^[a-z0-9\-]+$/',
            'description' => 'nullable|max:255',
            'sort_order'  => 'nullable|integer',
        ], [
            'slug.unique' => '이미 사용 중인 슬러그입니다.',
            'slug.regex'  => '슬러그는 영소문자, 숫자, 하이픈(-)만 사용 가능합니다.',
        ]);

        $data['slug']       = filled($data['slug'] ?? null)
            ? $data['slug']
            : (Str::slug($data['name']) ?: $category->slug);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $category->update($data);
        return redirect()->route('admin.categories.index')->with('success', '카테고리가 수정되었습니다.');
    }

    public function destroy(Category $category)
    {
        $postCount = Post::where('category', $category->name)->count();
        if ($postCount > 0) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', "카테고리를 삭제할 수 없습니다. '{$category->name}' 카테고리에 글이 {$postCount}개 있습니다. 먼저 글을 이동하거나 삭제해주세요.");
        }

        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', '카테고리가 삭제되었습니다.');
    }

    public function suggestions(Request $request)
    {
        $request->validate([
            'apply' => 'nullable|boolean',
        ]);

        $managerBaseUrl = rtrim((string) config('services.manager.base_url'), '/');
        if ($managerBaseUrl === '') {
            return back()->with('error', '관리 서버 주소가 설정되지 않았습니다. MANAGER_SERVER_URL 값을 확인해주세요.');
        }

        $blogUrl = rtrim((string) config('app.url'), '/');
        if ($blogUrl === '') {
            return back()->with('error', '블로그 URL(APP_URL)이 비어 있어 요청할 수 없습니다.');
        }

        $payload = [
            'url' => $blogUrl,
        ];

        if ($request->boolean('apply', false)) {
            $payload['apply'] = true;
        }

        $maxAttempts = 3;
        $lastError = '알 수 없는 오류';

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                Log::info('Category suggestion request started.', [
                    'attempt' => $attempt,
                    'max_attempts' => $maxAttempts,
                    'manager_base_url' => $managerBaseUrl,
                    'payload' => $payload,
                ]);

                $response = Http::timeout(20)
                    ->acceptJson()
                    ->asJson()
                    ->post($managerBaseUrl . '/api/blogs/category-suggestions', $payload);

                $json = $response->json();
                Log::info('Category suggestion response received.', [
                    'attempt' => $attempt,
                    'status' => $response->status(),
                    'success' => is_array($json) ? ($json['success'] ?? null) : null,
                    'suggested_categories' => is_array($json) ? ($json['data']['suggested_categories'] ?? null) : null,
                    'applied_categories' => is_array($json) ? ($json['data']['applied_categories'] ?? null) : null,
                    'message' => is_array($json) ? ($json['message'] ?? null) : null,
                    'raw_body' => Str::limit($response->body(), 1000),
                ]);

                if (!$response->successful()) {
                    $lastError = "외부 API HTTP {$response->status()}";
                } elseif (!is_array($json)) {
                    $lastError = '외부 API 응답 형식이 올바르지 않습니다.';
                } elseif (!($json['success'] ?? false)) {
                    $lastError = $json['message'] ?? '카테고리 추천 요청에 실패했습니다.';
                } else {
                    $data = $json['data'] ?? [];
                    $apply = (bool) ($data['apply'] ?? $request->boolean('apply', false));

                    if ($apply) {
                        $applied = collect($data['applied_categories'] ?? [])
                            ->map(fn ($item) => is_array($item) ? ($item['name'] ?? null) : $item)
                            ->filter()
                            ->values();

                        $count = $applied->count();
                        $list = $count > 0 ? ' (' . $applied->implode(', ') . ')' : '';

                        return back()->with('success', "카테고리 자동 생성 요청이 완료되었습니다. {$count}개 적용{$list}");
                    }

                    $suggested = collect($data['suggested_categories'] ?? [])->filter()->values();
                    $message = $suggested->isNotEmpty()
                        ? '추천 카테고리: ' . $suggested->implode(', ')
                        : '추천 카테고리를 받았지만 비어 있습니다.';

                    return back()->with('success', $message);
                }
            } catch (\Throwable $e) {
                $lastError = $e->getMessage();
                Log::error('Category suggestion request failed.', [
                    'attempt' => $attempt,
                    'max_attempts' => $maxAttempts,
                    'error' => $e->getMessage(),
                    'manager_base_url' => $managerBaseUrl,
                    'payload' => $payload,
                ]);
            }

            if ($attempt < $maxAttempts) {
                // 외부 서버 일시 장애를 고려해 짧게 텀을 두고 재시도
                usleep(500000);
            }
        }

        return back()->with('error', "카테고리 추천 요청이 {$maxAttempts}회 모두 실패했습니다. 마지막 오류: {$lastError}");
    }
}
// Test commit for auto-deploy
