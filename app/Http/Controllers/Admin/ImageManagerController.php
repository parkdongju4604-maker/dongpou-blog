<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ImageManagerController extends Controller
{
    public function index(Request $request): View
    {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];

        $allImages = collect(Storage::disk('public')->allFiles('uploads'))
            ->filter(function (string $path) use ($allowedExtensions) {
                $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                return in_array($ext, $allowedExtensions, true);
            })
            ->map(function (string $path) {
                return [
                    'path' => $path,
                    'name' => basename($path),
                    'url' => Storage::url($path),
                    'full_url' => url(Storage::url($path)),
                    'size_kb' => round(Storage::disk('public')->size($path) / 1024, 1),
                    'updated_at' => Storage::disk('public')->lastModified($path),
                ];
            })
            ->sortByDesc('updated_at')
            ->values();

        $perPage = 24;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $allImages->forPage($page, $perPage)->values();

        $images = new LengthAwarePaginator(
            $currentItems,
            $allImages->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.images.index', [
            'images' => $images,
            'totalImages' => $allImages->count(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'images' => 'required|array|min:1',
            'images.*' => 'required|image|mimes:jpeg,jpg,png,gif,webp,bmp,svg|max:10240',
        ], [
            'images.required' => '업로드할 이미지를 선택해주세요.',
            'images.array' => '업로드 형식이 올바르지 않습니다.',
            'images.*.image' => '이미지 파일만 업로드 가능합니다.',
            'images.*.mimes' => 'JPG, PNG, GIF, WEBP, BMP, SVG 형식만 지원합니다.',
            'images.*.max' => '파일 하나당 최대 10MB까지 업로드할 수 있습니다.',
        ]);

        $files = $request->file('images', []);
        foreach ($files as $file) {
            $dir = 'uploads/library/' . date('Y/m');
            $filename = time() . '_' . \Illuminate\Support\Str::random(8) . '.' . $file->getClientOriginalExtension();
            $file->storeAs($dir, $filename, 'public');
        }

        return redirect()->route('admin.images.index')->with('success', count($files) . '개 이미지가 업로드되었습니다.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'path' => 'required|string',
        ]);

        $path = (string) $data['path'];
        if (!str_starts_with($path, 'uploads/')) {
            return redirect()->route('admin.images.index')->with('error', '삭제 권한이 없는 경로입니다.');
        }

        if (!Storage::disk('public')->exists($path)) {
            return redirect()->route('admin.images.index')->with('error', '이미지를 찾을 수 없습니다.');
        }

        Storage::disk('public')->delete($path);

        return redirect()->route('admin.images.index')->with('success', '이미지가 삭제되었습니다.');
    }
}
