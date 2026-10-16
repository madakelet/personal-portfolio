{{-- Canonical, hreflang alternates and Open Graph for the current page. --}}
@php
    $route = request()->route();
    $params = $route ? \Illuminate\Support\Arr::only($route->parameters(), $route->parameterNames()) : [];
    $alternates = $route?->getName()
        ? collect(config('site.locales'))->mapWithKeys(fn ($label, $code) => [$code => route($route->getName(), [...$params, 'locale' => $code])])
        : collect();
@endphp
<link rel="canonical" href="{{ url()->current() }}">
@foreach ($alternates as $code => $href)
    <link rel="alternate" hreflang="{{ $code }}" href="{{ $href }}">
@endforeach
@if ($alternates->isNotEmpty())
    <link rel="alternate" hreflang="x-default" href="{{ route('root') }}">
@endif
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ config('site.name') }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:locale" content="{{ __('ui.meta.og_locale') }}">
