<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

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
        $request->validate([
            'name'        => 'required|string|max:100|unique:categories,name',
            'description' => 'nullable|string|max:500',
            'sort_order'  => 'nullable|integer|min:0',
        ], [
            'name.required' => 'name 필드는 필수입니다.',
            'name.unique'   => '이미 존재하는 카테고리입니다.',
        ]);

        $category = Category::create([
            'name'        => trim($request->name),
            'description' => $request->description,
            'sort_order'  => $request->sort_order ?? 0,
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
