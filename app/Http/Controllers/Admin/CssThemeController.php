<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CssTheme;
use Illuminate\Http\Request;

class CssThemeController extends Controller
{
    public function index()
    {
        $themes = CssTheme::orderBy('id')->get();
        return view('admin.themes.index', compact('themes'));
    }

    public function edit(CssTheme $theme)
    {
        return view('admin.themes.edit', compact('theme'));
    }

    public function update(Request $request, CssTheme $theme)
    {
        $request->validate([
            'name'          => 'required|string|max:100',
            'description'   => 'nullable|string|max:255',
            'preview_color' => 'nullable|string|max:20',
            'css'           => 'required|string',
        ]);

        $theme->update([
            'name'          => $request->name,
            'description'   => $request->description,
            'preview_color' => $request->preview_color ?? '#4f46e5',
            'css'           => $request->css,
        ]);

        CssTheme::clearCache();

        return back()->with('success', '테마가 저장되었습니다.');
    }

    public function activate(CssTheme $theme)
    {
        CssTheme::activate($theme->id);
        return back()->with('success', "'{$theme->name}' 테마가 활성화되었습니다.");
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:100',
            'description'   => 'nullable|string|max:255',
            'preview_color' => 'nullable|string|max:20',
        ]);

        $theme = CssTheme::create([
            'name'          => $request->name,
            'description'   => $request->description ?? '',
            'preview_color' => $request->preview_color ?? '#4f46e5',
            'css'           => '/* 여기에 CSS를 입력하세요 */',
            'is_active'     => false,
        ]);

        return redirect()->route('admin.themes.edit', $theme)->with('success', '새 테마가 생성되었습니다. CSS를 편집하세요.');
    }

    public function destroy(CssTheme $theme)
    {
        if ($theme->is_active) {
            return back()->with('error', '활성화된 테마는 삭제할 수 없습니다.');
        }
        if (CssTheme::count() <= 1) {
            return back()->with('error', '테마가 1개만 남으면 삭제할 수 없습니다.');
        }
        $theme->delete();
        CssTheme::clearCache();
        return back()->with('success', '테마가 삭제되었습니다.');
    }
}
