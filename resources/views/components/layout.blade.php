@props(['title' => null, 'description' => null, 'canonical' => null, 'alternates' => true, 'ogType' => 'website'])
@php
    $siteName = __('ui.name');
    $pageTitle = $title ? $title.' · '.$siteName : $siteName.' · '.__('ui.meta.tagline');
    $pageDescription = $description ?? __('ui.meta.description');
@endphp
<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    @include('partials.head-meta', ['title' => $pageTitle, 'description' => $pageDescription, 'canonical' => $canonical, 'withAlternates' => $alternates, 'ogType' => $ogType])
    {{-- Apply the stored theme before first paint so dark mode never flashes. --}}
    <script>try{var t=localStorage.getItem('theme');if(t==='light'||t==='dark')document.documentElement.dataset.theme=t}catch(e){}</script>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/site.js'])
</head>
<body>
    <canvas id="particles" class="particles" aria-hidden="true"></canvas>
    <div class="wrap">
        @include('partials.header')
        <main>
            {{ $slot }}
        </main>
        @include('partials.footer')
    </div>
</body>
</html>
