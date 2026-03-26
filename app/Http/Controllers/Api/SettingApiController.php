<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingApiController extends Controller
{
    /**
     * 허용된 설정 키 목록 (화이트리스트)
     * 그룹 정보 포함
     */
    private const ALLOWED = [
        // ── 기본 설정 ──────────────────────────
        'blog_name'                => ['group' => 'general',      'type' => 'string'],
        'blog_tagline'             => ['group' => 'general',      'type' => 'string'],
        'footer_text'              => ['group' => 'general',      'type' => 'string'],
        'hero_title'               => ['group' => 'general',      'type' => 'string'],
        'hero_subtitle'            => ['group' => 'general',      'type' => 'string'],
        'posts_per_page'           => ['group' => 'general',      'type' => 'integer'],
        // ── 디자인 ──────────────────────────────
        'primary_color'            => ['group' => 'appearance',   'type' => 'string'],
        // ── SEO ─────────────────────────────────
        'blog_description'         => ['group' => 'seo',          'type' => 'string'],
        'meta_keywords'            => ['group' => 'seo',          'type' => 'string'],
        'author_name'              => ['group' => 'seo',          'type' => 'string'],
        'author_nickname'          => ['group' => 'seo',          'type' => 'string'],
        'author_description'       => ['group' => 'seo',          'type' => 'string'],
        'author_slug'              => ['group' => 'seo',          'type' => 'string'],
        'og_image_default'         => ['group' => 'seo',          'type' => 'string'],
        'robots_index'             => ['group' => 'seo',          'type' => 'string'],
        'google_analytics'         => ['group' => 'seo',          'type' => 'string'],
        'twitter_handle'           => ['group' => 'seo',          'type' => 'string'],
        'kakao_js_key'             => ['group' => 'seo',          'type' => 'string'],
        'head_code'                => ['group' => 'seo',          'type' => 'string'],
        // ── 인증 코드 ────────────────────────────
        'google_site_verification' => ['group' => 'verification', 'type' => 'string'],
        'naver_site_verification'  => ['group' => 'verification', 'type' => 'string'],
    ];

    /**
     * GET /api/settings
     * 전체 설정 조회
     */
    public function index()
    {
        return response()->json([
            'data' => $this->currentSettings(),
        ]);
    }

    /**
     * PATCH /api/settings
     * 전달된 키만 수정 (나머지는 유지)
     */
    public function update(Request $request)
    {
        $input = $request->all();

        // 허용된 키만 필터링
        $toUpdate = array_intersect_key($input, self::ALLOWED);

        if (empty($toUpdate)) {
            return response()->json([
                'message' => '변경할 설정이 없습니다. 허용된 키를 확인하세요.',
                'allowed_keys' => array_keys(self::ALLOWED),
            ], 422);
        }

        // 유효성 검사
        $rules = [];
        foreach ($toUpdate as $key => $value) {
            $meta = self::ALLOWED[$key];
            if ($meta['type'] === 'integer') {
                $rules[$key] = 'nullable|integer|min:1|max:100';
            } else {
                $rules[$key] = 'nullable|string|max:2000';
            }
        }
        $request->validate($rules);

        // 저장
        $updated = [];
        foreach ($toUpdate as $key => $value) {
            Setting::set($key, $value ?? '');
            $updated[] = $key;
        }

        return response()->json([
            'message' => count($updated) . '개 설정이 업데이트되었습니다.',
            'updated' => $updated,
            'data'    => $this->currentSettings(),
        ]);
    }

    /** 현재 전체 설정 반환 */
    private function currentSettings(): array
    {
        $result = [];
        foreach (self::ALLOWED as $key => $meta) {
            $result[$meta['group']][$key] = Setting::get($key);
        }
        return $result;
    }
}
