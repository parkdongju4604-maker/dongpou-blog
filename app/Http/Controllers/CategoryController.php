<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::ordered()->get();
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
            : (Str::slug($data['name']) ?: Str::random(8));
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
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', '카테고리가 삭제되었습니다.');
    }
}
// Test commit for auto-deploy
