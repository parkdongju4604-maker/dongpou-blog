<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;

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
                'id'   => $c->id,
                'name' => $c->name,
                'slug' => $c->slug,
            ]),
        ]);
    }
}
