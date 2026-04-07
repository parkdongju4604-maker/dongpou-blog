<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CssTheme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

class CssThemeController extends Controller
{
    public function index()
    {
        // 라우트 캐시가 오래돼 sync 라우트가 없으면 자동 초기화 후 새로고침
        if (! app('router')->has('admin.themes.sync')) {
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            return redirect()->back();
        }

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

    public function sync()
    {
        try {
            // 캐시 초기화 (라우트 캐시 등 배포 후 불일치 방지)
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            Artisan::call('config:clear');

            $response = Http::timeout(15)->get('http://mango-ai.co.kr/api/css-files');

            if (! $response->successful()) {
                return back()->with('error', '외부 API 호출에 실패했습니다. (HTTP ' . $response->status() . ')');
            }

            $json = $response->json();

            if (! isset($json['result']) || $json['result'] !== 1 || ! isset($json['data'])) {
                return back()->with('error', 'API 응답 형식이 올바르지 않습니다.');
            }

            $existingThemes = CssTheme::get()->keyBy(
                fn($theme) => mb_strtolower(trim($theme->name))
            );

            $added = 0;
            $updated = 0;
            foreach ($json['data'] as $item) {
                $itemName = trim((string) ($item['name'] ?? ''));
                if ($itemName === '') {
                    continue;
                }

                $normalizedName = mb_strtolower($itemName);
                $itemCss = (string) ($item['content'] ?? '');
                $existingTheme = $existingThemes->get($normalizedName);

                if ($existingTheme) {
                    $existingTheme->update([
                        'name' => $itemName,
                        'css'  => $itemCss,
                    ]);
                    $updated++;
                    continue;
                }

                $createdTheme = CssTheme::create([
                    'name'          => $itemName,
                    'description'   => $itemName . ' 테마',
                    'preview_color' => '#4f46e5',
                    'css'           => $itemCss,
                    'is_active'     => false,
                ]);

                $existingThemes->put($normalizedName, $createdTheme);
                $added++;
            }

            if ($added === 0 && $updated === 0) {
                return back()->with('success', '추가/업데이트할 테마가 없습니다. 서버 캐시는 초기화되었습니다.');
            }

            CssTheme::clearCache();

            return back()->with(
                'success',
                "테마 동기화 완료: 신규 {$added}개, 업데이트 {$updated}개 (서버 캐시 초기화됨)"
            );

        } catch (\Exception $e) {
            return back()->with('error', '동기화 중 오류가 발생했습니다: ' . $e->getMessage());
        }
    }
}
