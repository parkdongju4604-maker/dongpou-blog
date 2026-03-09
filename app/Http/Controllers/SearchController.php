<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q     = trim($request->get('q', ''));
        $posts = collect();
        $total = 0;

        if (mb_strlen($q) >= 2) {
            $posts = Post::published()
                ->where(function ($query) use ($q) {
                    $query->where('title',    'LIKE', "%{$q}%")
                          ->orWhere('excerpt', 'LIKE', "%{$q}%")
                          ->orWhere('content', 'LIKE', "%{$q}%")
                          ->orWhere('category','LIKE', "%{$q}%");
                })
                ->paginate(10)
                ->withQueryString();

            $total = $posts->total();
        }

        return view('search.index', compact('q', 'posts', 'total'));
    }

    /**
     * 키워드 주변 스니펫 추출 + 하이라이팅
     */
    public static function snippet(string $text, string $q, int $length = 160): string
    {
        $plain = strip_tags($text);
        $pos   = mb_stripos($plain, $q);

        if ($pos !== false) {
            $start   = max(0, $pos - 60);
            $snippet = mb_substr($plain, $start, $length);
            if ($start > 0)          $snippet = '…' . $snippet;
            if (mb_strlen($plain) > $start + $length) $snippet .= '…';
        } else {
            $snippet = mb_substr($plain, 0, $length) . '…';
        }

        // 키워드 하이라이팅
        return preg_replace(
            '/(' . preg_quote(e($q), '/') . ')/iu',
            '<mark>$1</mark>',
            e($snippet)
        );
    }
}
