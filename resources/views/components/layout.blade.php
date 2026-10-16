@props(['title' => null, 'description' => null])
@php
    $siteName = config('site.name');
    $pageTitle = $title ? $title.' · '.$siteName : $siteName.' · '.__('ui.meta.tagline');
@endphp
<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $description ?? __('ui.meta.description') }}">
    {{-- Apply the stored theme before first paint so dark mode never flashes. --}}
    <script>try{var t=localStorage.getItem('theme');if(t==='light'||t==='dark')document.documentElement.dataset.theme=t}catch(e){}</script>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/site.js'])
</head>
<body>
    <div class="wrap">
        @include('partials.header')
        <main>
            {{ $slot }}
        </main>
        @include('partials.footer')
    </div>
</body>
</html>
