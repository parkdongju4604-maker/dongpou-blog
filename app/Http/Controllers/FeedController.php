<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Setting;
use Illuminate\Http\Response;

class FeedController extends Controller
{
    private function feedData(): array
    {
        $posts = Post::published()->limit(20)->get();

        return [
            'blogName'    => Setting::get('blog_name',        config('app.name')),
            'blogDesc'    => Setting::get('blog_description', Setting::get('blog_tagline', '')),
            'blogUrl'     => url('/'),
            'feedUrl'     => url('/feed'),
            'authorName'  => Setting::get('author_name', Setting::get('blog_name', config('app.name'))),
            'posts'       => $posts,
            'updatedAt'   => $posts->first()?->published_at ?? now(),
        ];
    }

    /** RSS 2.0 */
    public function rss(): Response
    {
        $data = $this->feedData();
        $xml  = view('feeds.rss', $data)->render();

        return response($xml, 200, [
            'Content-Type'  => 'application/rss+xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /** Atom 1.0 */
    public function atom(): Response
    {
        $data = $this->feedData();
        $xml  = view('feeds.atom', $data)->render();

        return response($xml, 200, [
            'Content-Type'  => 'application/atom+xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
