<x-layout :title="__('ui.sections.blog')" :description="__('ui.blog.intro')">
    <h1>{{ __('ui.sections.blog') }}</h1>
    <p class="muted mt-4">{{ __('ui.blog.intro') }}</p>

    @forelse ($years as $year => $posts)
        <h2 class="year">{{ $year }}</h2>
        <ul class="dated">
            @foreach ($posts as $post)
                <li>
                    <span class="d">{{ \App\Support\PostDate::short($post->published_at) }}</span>
                    <span lang="{{ $post->locale }}">
                        <a href="{{ route('blog.show', ['slug' => $post->slug]) }}">{{ $post->title }}</a>@include('partials.post-language')
                        @if ($post->excerpt)
                            <span class="sub">{{ $post->excerpt }}</span>
                        @endif
                    </span>
                </li>
            @endforeach
        </ul>
    @empty
        <p>{{ __('ui.blog.empty') }}</p>
    @endforelse
</x-layout>
