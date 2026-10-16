{{-- " · in Hungarian" when a post is written in another language than the page. --}}
@if ($post->locale !== app()->getLocale())
    <span class="muted"> · {{ __('ui.blog.written_in', ['language' => __("ui.languages.{$post->locale}")]) }}</span>
@endif
