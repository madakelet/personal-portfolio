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

    @if ($projects->isNotEmpty())
        <h2 id="projects">{{ __('ui.sections.projects') }}</h2>
        <ul>
            @foreach ($projects as $project)
                <li><strong>@if ($project->url)<a href="{{ $project->url }}" target="_blank" rel="noopener">{{ $project->translated('title') }}</a>@else{{ $project->translated('title') }}@endif</strong>@if ($project->translated('meta') !== '') <span class="muted">({{ $project->translated('meta') }})</span>@endif. {!! $project->translated('body') !!}</li>
            @endforeach
        </ul>
    @endif
</x-layout>
