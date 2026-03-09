<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageApiController extends Controller
{
    /**
     * POST /api/images
     *
     * multipart/form-data:
     *   image: 이미지 파일 (jpeg, jpg, png, gif, webp, max 10MB)
     *
     * Response:
     *   { "data": { "url": "https://...", "path": "uploads/posts/..." } }
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:10240',
        ], [
            'image.required' => 'image 파일을 전송해주세요.',
            'image.image'    => '이미지 파일만 업로드 가능합니다.',
            'image.mimes'    => 'JPG, PNG, GIF, WEBP 형식만 지원합니다.',
            'image.max'      => '파일 크기는 10MB를 초과할 수 없습니다.',
        ]);

        $file     = $request->file('image');
        $dir      = 'uploads/posts/' . date('Y/m');
        $filename = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $path     = $file->storeAs($dir, $filename, 'public');

        // 절대 URL 반환 (API 소비자가 외부에서 접근 가능한 URL)
        $absoluteUrl = url(Storage::url($path));

        return response()->json([
            'data' => [
                'url'      => $absoluteUrl,
                'path'     => $path,
                'filename' => $filename,
                'size'     => $file->getSize(),
                'mime'     => $file->getMimeType(),
            ],
            'message' => '이미지가 업로드되었습니다.',
        ], 201);
    }
}
