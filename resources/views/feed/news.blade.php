{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{{ $siteName }} — Новини</title>
        <link>{{ route('news.index') }}</link>
        <description>{{ $description }}</description>
        <language>uk</language>
        <lastBuildDate>{{ now()->toRfc2822String() }}</lastBuildDate>
        <atom:link href="{{ route('news.feed') }}" rel="self" type="application/rss+xml"/>
        @foreach ($news as $item)
            <item>
                <title>{{ $item->title }}</title>
                <link>{{ route('news.show', $item) }}</link>
                <guid isPermaLink="true">{{ route('news.show', $item) }}</guid>
                @if ($item->published_at)
                    <pubDate>{{ $item->published_at->copy()->shiftTimezone('Europe/Kyiv')->toRfc2822String() }}</pubDate>
                @endif
                @if ($item->excerpt)
                    <description>{{ $item->excerpt }}</description>
                @endif
            </item>
        @endforeach
    </channel>
</rss>