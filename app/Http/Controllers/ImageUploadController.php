<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageUploadController extends Controller
{
    /**
     * 이미지 업로드 → /storage/uploads/posts/YYYY/MM/ 에 저장
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:10240', // 최대 10MB
        ], [
            'image.image'   => '이미지 파일만 업로드 가능합니다.',
            'image.mimes'   => 'JPG, PNG, GIF, WEBP 형식만 지원합니다.',
            'image.max'     => '파일 크기는 10MB를 초과할 수 없습니다.',
        ]);

        $file = $request->file('image');
        $dir  = 'uploads/posts/' . date('Y/m');

        // 원본 파일명 기반으로 유니크 파일명 생성
        $filename = time() . '_' . \Illuminate\Support\Str::random(8) . '.' . $file->getClientOriginalExtension();
        $path     = $file->storeAs($dir, $filename, 'public');

        return response()->json([
            'url'  => Storage::url($path),
            'path' => $path,
        ]);
    }

    /**
     * 이미지 삭제
     */
    public function destroy(Request $request)
    {
        $request->validate(['path' => 'required|string']);

        $path = $request->input('path');

        // uploads/posts/ 경로에 있는 파일만 삭제 허용 (보안)
        if (!str_starts_with($path, 'uploads/posts/')) {
            return response()->json(['error' => '삭제 권한이 없습니다.'], 403);
        }

        Storage::disk('public')->delete($path);

        return response()->json(['success' => true]);
    }
}
