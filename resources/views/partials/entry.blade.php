{{-- One experience or education entry: bold title, date range on the right, a paragraph. --}}
<div class="job">
    <div class="head"><strong>{{ $entry['title'] }}</strong><span class="d">{{ \App\Support\DateRange::format($entry['from'], $entry['to']) }}</span></div>
    <p>{!! $entry['body'] !!}</p>
</div>
