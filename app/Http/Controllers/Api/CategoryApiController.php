<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryApiController extends Controller
{
    /**
     * GET /api/categories
     * 카테고리 목록 반환
     */
    public function index()
    {
        $categories = Category::ordered()->get(['id', 'name', 'slug']);

        return response()->json([
            'data' => $categories->map(fn($c) => [
                'id'         => $c->id,
                'name'       => $c->name,
                'slug'       => $c->slug,
                'post_count' => Post::where('category', $c->name)->count(),
            ]),
        ]);
    }

    /**
     * POST /api/categories
     * 카테고리 생성
     */
    public function store(Request $request)
    {
        $normalizedName = $this->normalizeCategoryName((string) $request->input('name', ''));

        if ($normalizedName === '') {
            return response()->json([
                'message' => '유효한 카테고리 이름이 아닙니다.',
                'errors'  => ['name' => ['name 필드는 필수입니다.']],
            ], 422);
        }

        if ($this->looksMalformedCategoryName($normalizedName)) {
            Log::warning('Rejected malformed category name from API.', [
                'received' => $request->input('name'),
                'normalized' => $normalizedName,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'message' => '카테고리 이름 형식이 올바르지 않습니다.',
                'errors'  => ['name' => ['JSON 조각/특수문자가 포함된 값은 허용되지 않습니다.']],
            ], 422);
        }

        $validated = validator([
            'name' => $normalizedName,
            'description' => $request->input('description'),
            'sort_order' => $request->input('sort_order'),
        ], [
            'name'        => 'required|string|max:100|unique:categories,name',
            'description' => 'nullable|string|max:500',
            'sort_order'  => 'nullable|integer|min:0',
        ], [
            'name.required' => 'name 필드는 필수입니다.',
            'name.unique'   => '이미 존재하는 카테고리입니다.',
        ])->validate();

        $category = Category::create([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'sort_order'  => $validated['sort_order'] ?? 0,
        ]);

        return response()->json([
            'message' => '카테고리가 생성되었습니다.',
            'data'    => [
                'id'   => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
            ],
        ], 201);
    }

    private function normalizeCategoryName(string $name): string
    {
        $name = trim($name);
        $name = preg_replace('/\s+/u', ' ', $name) ?? $name;

        // 외부 파싱 오류로 끝에 붙는 따옴표/콤마/마침표 등을 제거
        return trim($name, " \t\n\r\0\x0B\"'`,.");
    }

    private function looksMalformedCategoryName(string $name): bool
    {
        if (preg_match('/[\{\}\[\]"]/u', $name)) {
            return true;
        }

        // JSON 키/응답 조각이 카테고리명으로 들어오는 경우 차단
        if (preg_match('/\b(categories?|suggested_categories|existing_categories|apply|success|data)\b/i', $name)) {
            return true;
        }

        return false;
    }

    /**
     * DELETE /api/categories/{id}
     * 카테고리 삭제 (글이 있으면 거부)
     */
    public function destroy(Category $category)
    {
        $postCount = Post::where('category', $category->name)->count();

        if ($postCount > 0) {
            return response()->json([
                'message' => "카테고리를 삭제할 수 없습니다. 해당 카테고리에 글이 {$postCount}개 있습니다.",
                'post_count' => $postCount,
            ], 422);
        }

        $category->delete();

        return response()->json([
            'message' => '카테고리가 삭제되었습니다.',
        ]);
    }
}
