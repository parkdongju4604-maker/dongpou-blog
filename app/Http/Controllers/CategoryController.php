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
            'description' => 'nullable|max:255',
            'sort_order'  => 'nullable|integer',
        ]);
        $data['slug']       = Str::slug($data['name']) ?: Str::random(8);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        Category::create($data);
        return redirect()->route('admin.categories.index')->with('success', '카테고리가 추가되었습니다.');
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'        => 'required|max:100|unique:categories,name,' . $category->id,
            'description' => 'nullable|max:255',
            'sort_order'  => 'nullable|integer',
        ]);
        $data['slug']       = Str::slug($data['name']) ?: $category->slug;
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
