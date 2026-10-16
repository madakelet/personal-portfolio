<x-layout
    :title="$post->title"
    :description="$post->excerpt"
    :canonical="route('blog.show', ['locale' => $post->locale, 'slug' => $post->slug])"
    :alternates="false"
    og-type="article"
>
    <article lang="{{ $post->locale }}">
        <h1>{{ $post->title }}</h1>
        <p class="meta">
            <time datetime="{{ $post->published_at->toDateString() }}">{{ \App\Support\PostDate::full($post->published_at) }}</time>@include('partials.post-language')
        </p>
        <div class="post-body">
            {!! $post->body_html !!}
        </div>
    </article>

    @if ($older || $newer)
        <nav class="pager" aria-label="{{ __('ui.sections.blog') }}">
            <span>@if ($older)← <a href="{{ route('blog.show', ['slug' => $older->slug]) }}">{{ $older->title }}</a>@endif</span>
            <span>@if ($newer)<a href="{{ route('blog.show', ['slug' => $newer->slug]) }}">{{ $newer->title }}</a> →@endif</span>
        </nav>
    @endif
</x-layout>
