<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\Setting;

class SitemapController extends Controller
{
    public function sitemap()
    {
        $posts      = Post::published()->orderByDesc('updated_at')->get();
        $categoryNames = Post::published()->reorder()->distinct()->pluck('category');
        $categorySlugs = Category::query()->pluck('slug', 'name');
        $categories = $categoryNames->map(function (string $name) use ($categorySlugs) {
            return [
                'name' => $name,
                'slug' => $categorySlugs[$name] ?? rawurlencode($name),
            ];
        });
        $tags       = Tag::has('posts')->get();
        $baseUrl    = rtrim(Setting::get('app_url', config('app.url')), '/');

        return response()->view('sitemap', compact('posts', 'categories', 'tags', 'baseUrl'))
            ->header('Content-Type', 'application/xml');
    }

    public function robots()
    {
        $baseUrl  = rtrim(config('app.url'), '/');
        $robotsVal = Setting::get('robots_index', 'index,follow');

        $content = "User-agent: *\n";
        if (str_contains($robotsVal, 'noindex')) {
            $content .= "Disallow: /\n";
        } else {
            $content .= "Allow: /\n";
            $content .= "Disallow: /admin\n";
            $content .= "Disallow: /admin/*\n";
        }
        $content .= "\nSitemap: {$baseUrl}/sitemap.xml\n";

        return response($content)->header('Content-Type', 'text/plain');
    }
}
