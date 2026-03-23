<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:news="http://www.google.com/schemas/sitemap-news/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">

    {{-- 홈 --}}
    <url>
        <loc>{{ $baseUrl }}/</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
    </url>

    {{-- 카테고리 --}}
    @foreach($categories as $category)
    <url>
        <loc>{{ $baseUrl }}/{{ $category['slug'] }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
    </url>
    @endforeach

    {{-- 태그 --}}
    @foreach($tags as $tag)
    <url>
        <loc>{{ $baseUrl }}/tags/{{ $tag->slug }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.5</priority>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
    </url>
    @endforeach

    {{-- 포스트 --}}
    @foreach($posts as $post)
    <url>
        <loc>{{ route('posts.show', ['categorySlug' => $post->category_path_segment, 'slug' => $post->slug]) }}</loc>
        <lastmod>{{ $post->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach

</urlset>
