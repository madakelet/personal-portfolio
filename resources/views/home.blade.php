<x-layout>
    <h1>{{ __('ui.name') }}</h1>

    <div class="mt-4">
        @foreach (__('cv.bio') as $paragraph)
            <p>{!! $paragraph !!}</p>
        @endforeach
        <p>
            {!! __('cv.contact', ['github' => config('site.github'), 'linkedin' => config('site.linkedin'), 'email' => config('site.email')]) !!}
            @if ($cvUrl)
                {!! __('cv.cv_pdf', ['url' => $cvUrl]) !!}
            @endif
        </p>
    </div>

    <h2 id="experience">{{ __('ui.sections.experience') }}</h2>
    @foreach (__('cv.experience') as $entry)
        @include('partials.entry')
    @endforeach

    <h2 id="education">{{ __('ui.sections.education') }}</h2>
    @foreach (__('cv.education') as $entry)
        @include('partials.entry')
    @endforeach

    <h2 id="projects">{{ __('ui.sections.projects') }}</h2>
    <ul>
        @foreach (__('cv.projects') as $project)
            <li><strong>{{ $project['title'] }}</strong> <span class="muted">({{ $project['meta'] }})</span>. {!! $project['body'] !!}</li>
        @endforeach
    </ul>

    <h2 id="side-projects">{{ __('ui.sections.side_projects') }}</h2>
    <ul>
        @foreach (__('cv.side_projects') as $key => $project)
            @php($url = config("site.side_project_urls.{$key}"))
            <li><strong>@if ($url)<a href="{{ $url }}" target="_blank" rel="noopener">{{ $project['title'] }}</a>@else{{ $project['title'] }}@endif</strong> <span class="muted">({{ $project['meta'] }})</span>. {!! $project['body'] !!}</li>
        @endforeach
    </ul>
</x-layout>
