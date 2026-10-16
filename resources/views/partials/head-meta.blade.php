{{-- Canonical, hreflang alternates and Open Graph for the current page.
     Blog posts pass their own canonical URL and no alternates: a post is
     written in one language, so the other prefixes are not translations. --}}
@php
    $route = request()->route();
    $params = $route ? \Illuminate\Support\Arr::only($route->parameters(), $route->parameterNames()) : [];
    $canonical ??= url()->current();
    $alternates = ($withAlternates ?? true) && $route?->getName()
        ? collect(config('site.locales'))->mapWithKeys(fn ($label, $code) => [$code => route($route->getName(), [...$params, 'locale' => $code])])
        : collect();
@endphp
<link rel="canonical" href="{{ $canonical }}">
@foreach ($alternates as $code => $href)
    <link rel="alternate" hreflang="{{ $code }}" href="{{ $href }}">
@endforeach
@if ($alternates->isNotEmpty())
    <link rel="alternate" hreflang="x-default" href="{{ route('root') }}">
@endif
<meta property="og:type" content="{{ $ogType ?? 'website' }}">
<meta property="og:site_name" content="{{ __('ui.name') }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:locale" content="{{ __('ui.meta.og_locale') }}">
