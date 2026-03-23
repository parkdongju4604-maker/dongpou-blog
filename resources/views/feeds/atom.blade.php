<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<feed xmlns="http://www.w3.org/2005/Atom" xml:lang="ko">
  <title>{{ $blogName }}</title>
  <subtitle>{{ $blogDesc }}</subtitle>
  <id>{{ $blogUrl }}/</id>
  <link href="{{ $blogUrl }}" rel="alternate" type="text/html"/>
  <link href="{{ url('/feed/atom') }}" rel="self" type="application/atom+xml"/>
  <updated>{{ $updatedAt->toAtomString() }}</updated>
  <author>
    <name>{{ $authorName }}</name>
  </author>
  <generator uri="https://dongpou.com">DongPou Blog</generator>

  @foreach($posts as $post)
  @php
      $postUrl = route('posts.show', ['categorySlug' => $post->category_path_segment, 'slug' => $post->slug]);
      $summary = $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 200);
  @endphp
  <entry>
    <title type="html"><![CDATA[{{ $post->title }}]]></title>
    <id>{{ $postUrl }}</id>
    <link href="{{ $postUrl }}" rel="alternate" type="text/html"/>
    <published>{{ $post->published_at->toAtomString() }}</published>
    <updated>{{ $post->updated_at->toAtomString() }}</updated>
    <author><name>{{ $authorName }}</name></author>
    <category term="{{ $post->category }}"/>
    <summary type="html"><![CDATA[{{ $summary }}]]></summary>
    <content type="html"><![CDATA[{!! $post->rendered_content !!}]]></content>
  </entry>
  @endforeach

</feed>
