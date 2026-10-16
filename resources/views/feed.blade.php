{!! '<'.'?xml version="1.0" encoding="UTF-8"?>' !!}
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:content="http://purl.org/rss/1.0/modules/content/">
    <channel>
        <title>{{ __('ui.name', [], 'en') }}</title>
        <link>{{ route('root') }}</link>
        <description>{{ __('ui.blog.intro', [], 'en') }}</description>
        <language>en</language>
        <atom:link href="{{ route('feed') }}" rel="self" type="application/rss+xml" />
        @if ($posts->isNotEmpty())
            <lastBuildDate>{{ $posts->first()->published_at->toRssString() }}</lastBuildDate>
        @endif
        @foreach ($posts as $post)
            @php($url = route('blog.show', ['locale' => $post->locale, 'slug' => $post->slug]))
            <item>
                <title>{{ $post->title }}</title>
                <link>{{ $url }}</link>
                <guid isPermaLink="true">{{ $url }}</guid>
                <pubDate>{{ $post->published_at->toRssString() }}</pubDate>
                @if ($post->excerpt)
                    <description>{{ $post->excerpt }}</description>
                @endif
                <content:encoded><![CDATA[{!! str_replace(']]>', ']]]]><![CDATA[>', $post->body_html) !!}]]></content:encoded>
            </item>
        @endforeach
    </channel>
</rss>
