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
     *   category_slug: 카테고리 슬러그 (선택, 기본값: uncategorized)
     *   title: 이미지 제목 (선택)
     *   alt_text: 이미지 대체 텍스트 (선택)
     *
     * Response:
     *   { "data": { "url": "https://...", "path": "uploads/posts/..." } }
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:10240',
            'category_slug' => 'nullable|string|max:100',
            'title' => 'nullable|string|max:255',
            'alt_text' => 'nullable|string|max:255',
        ], [
            'image.required' => 'image 파일을 전송해주세요.',
            'image.image'    => '이미지 파일만 업로드 가능합니다.',
            'image.mimes'    => 'JPG, PNG, GIF, WEBP 형식만 지원합니다.',
            'image.max'      => '파일 크기는 10MB를 초과할 수 없습니다.',
            'category_slug.string' => 'category_slug는 문자열이어야 합니다.',
            'category_slug.max'    => 'category_slug는 100자를 초과할 수 없습니다.',
            'title.string'   => 'title은 문자열이어야 합니다.',
            'title.max'      => 'title은 255자를 초과할 수 없습니다.',
            'alt_text.string'=> 'alt_text는 문자열이어야 합니다.',
            'alt_text.max'   => 'alt_text는 255자를 초과할 수 없습니다.',
        ]);

        $file = $request->file('image');
        $dir = 'uploads/posts/' . date('Y/m');
        $timestamp = time();
        $rawCategorySlug = isset($validated['category_slug']) ? trim($validated['category_slug']) : '';
        $normalizedCategorySlug = trim((string) preg_replace('/[^\pL\pN]+/u', '-', $rawCategorySlug), '-');
        $categorySlug = $normalizedCategorySlug !== '' ? $normalizedCategorySlug : 'uncategorized';
        $defaultLabel = $categorySlug . '-image_' . $timestamp;

        $title = isset($validated['title']) ? trim($validated['title']) : '';
        $altText = isset($validated['alt_text']) ? trim($validated['alt_text']) : '';
        $resolvedTitle = $title !== '' ? $title : $defaultLabel;
        $resolvedAltText = $altText !== '' ? $altText : $defaultLabel;

        $baseTitle = trim((string) preg_replace('/[^\pL\pN]+/u', '-', $resolvedTitle), '-');
        $safeTitle = Str::limit($baseTitle !== '' ? $baseTitle : 'image', 60, '');
        $filename = $safeTitle . '_' . $timestamp . '_' . Str::random(4) . '.' . $file->getClientOriginalExtension();
        $path     = $file->storeAs($dir, $filename, 'public');

        // 절대 URL 반환 (API 소비자가 외부에서 접근 가능한 URL)
        $absoluteUrl = url(Storage::url($path));
        $normalizedTitle = $resolvedTitle;
        $normalizedAltText = $resolvedAltText;
        $markdownAlt = $normalizedAltText;
        $markdownTitle = $normalizedTitle ? ' "' . addslashes($normalizedTitle) . '"' : '';

        $htmlAttrs = '';
        if ($normalizedAltText) {
            $htmlAttrs .= ' alt="' . e($normalizedAltText) . '"';
        }
        if ($normalizedTitle) {
            $htmlAttrs .= ' title="' . e($normalizedTitle) . '"';
        }
        $htmlTag = '<img src="' . e($absoluteUrl) . '"' . $htmlAttrs . ' />';

        return response()->json([
            'data' => [
                'url'      => $absoluteUrl,
                'path'     => $path,
                'filename' => $filename,
                'size'     => $file->getSize(),
                'mime'     => $file->getMimeType(),
                'title'    => $normalizedTitle,
                'alt_text' => $normalizedAltText,
                'markdown' => '![' . $markdownAlt . '](' . $absoluteUrl . $markdownTitle . ')',
                'html'     => $htmlTag,
            ],
            'message' => '이미지가 업로드되었습니다.',
        ], 201);
    }
}
