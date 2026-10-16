<header class="site-header">
    <a class="name" href="{{ route('home') }}">{{ __('ui.header_name') }}</a>
    <nav aria-label="{{ __('ui.nav.label') }}">
        <a href="{{ route('home') }}" @if (request()->routeIs('home')) aria-current="page" @endif>{{ __('ui.nav.about') }}</a>
        <a href="{{ route('home') }}#projects">{{ __('ui.nav.projects') }}</a>
        @if ($cvUrl)
            <a href="{{ $cvUrl }}">{{ __('ui.nav.cv') }}</a>
        @endif
        <button type="button" id="theme" aria-label="{{ __('ui.nav.theme') }}">auto</button>
        @include('partials.locale-switcher')
    </nav>
</header>
