<header class="site-header">
    <a class="name" href="{{ route('home') }}">{{ config('site.name') }}</a>
    <nav aria-label="{{ __('ui.nav.label') }}">
        <a href="{{ route('home') }}" @if (request()->routeIs('home')) aria-current="page" @endif>{{ __('ui.nav.about') }}</a>
        <a href="{{ route('home') }}#projects">{{ __('ui.nav.projects') }}</a>
        @if (file_exists(public_path(config('site.cv_pdf'))))
            <a href="{{ asset(config('site.cv_pdf')) }}">{{ __('ui.nav.cv') }}</a>
        @endif
        <button type="button" id="theme" aria-label="{{ __('ui.nav.theme') }}">auto</button>
    </nav>
</header>
