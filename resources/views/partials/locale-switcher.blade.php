{{-- Same page in each locale. Plain links: the server stores the choice on the next request. --}}
@php
    $route = request()->route();
    // Only real URL parameters; Route::view() also stores its view name as one.
    $params = $route ? \Illuminate\Support\Arr::only($route->parameters(), $route->parameterNames()) : [];
@endphp
@if ($route?->getName())
    <span class="locales" role="group" aria-label="{{ __('ui.nav.language') }}">
        @foreach (config('site.locales') as $code => $label)
            <a href="{{ route($route->getName(), [...$params, 'locale' => $code]) }}" hreflang="{{ $code }}" lang="{{ $code }}" title="{{ $label }}"@if ($code === app()->getLocale()) aria-current="true"@endif>{{ $code }}</a>
        @endforeach
    </span>
@endif
