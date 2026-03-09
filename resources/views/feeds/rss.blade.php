<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<rss version="2.0"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:dc="http://purl.org/dc/elements/1.1/">
  <channel>
    <title>{{ $blogName }}</title>
    <link>{{ $blogUrl }}</link>
    <description>{{ $blogDesc }}</description>
    <language>ko</language>
    <lastBuildDate>{{ now()->toRfc2822String() }}</lastBuildDate>
    <atom:link href="{{ url('/feed/rss') }}" rel="self" type="application/rss+xml"/>
    <generator>DongPou Blog</generator>

    @foreach($posts as $post)
    @php
        $postUrl = route('posts.show', $post->slug);
        $excerpt = $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 200);
    @endphp
    <item>
      <title><![CDATA[{{ $post->title }}]]></title>
      <link>{{ $postUrl }}</link>
      <guid isPermaLink="true">{{ $postUrl }}</guid>
      <pubDate>{{ $post->published_at->toRfc2822String() }}</pubDate>
      <dc:creator><![CDATA[{{ $authorName }}]]></dc:creator>
      <category><![CDATA[{{ $post->category }}]]></category>
      <description><![CDATA[{{ $excerpt }}]]></description>
      <content:encoded><![CDATA[{!! $post->rendered_content !!}]]></content:encoded>
    </item>
    @endforeach

  </channel>
</rss>
