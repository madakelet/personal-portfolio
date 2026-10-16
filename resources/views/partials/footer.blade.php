<footer class="site-footer">
    <span>© {{ date('Y') }} {{ __('ui.name') }}</span>
    @if (config('site.github'))
        <a href="{{ config('site.github') }}" rel="noopener" target="_blank">GitHub</a>
    @endif
    @if (config('site.linkedin'))
        <a href="{{ config('site.linkedin') }}" rel="noopener" target="_blank">LinkedIn</a>
    @endif
</footer>
