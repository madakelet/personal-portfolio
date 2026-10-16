<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Projects', [
            'locales' => config('site.locales'),
            'projects' => Project::query()->ordered()->get(['id', 'position', ...Project::FIELDS]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $project = Project::create([
            ...$this->validated($request),
            'position' => (int) Project::max('position') + 1,
        ]);

        return back()->with('status', "Added \"{$project->translated('title', 'en')}\".");
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $project->update($this->validated($request));

        return back()->with('status', "Saved \"{$project->translated('title', 'en')}\".");
    }

    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();
        $this->renumber();

        return back()->with('status', "Removed \"{$project->translated('title', 'en')}\".");
    }

    /** Swap a project with its neighbour above or below. */
    public function move(Request $request, Project $project): RedirectResponse
    {
        $direction = $request->validate(['direction' => ['required', Rule::in(['up', 'down'])]])['direction'];
        $ordered = Project::query()->ordered()->pluck('id')->values();
        $index = $ordered->search($project->id);
        $target = $direction === 'up' ? $index - 1 : $index + 1;

        if ($target >= 0 && $target < $ordered->count()) {
            [$ordered[$index], $ordered[$target]] = [$ordered[$target], $ordered[$index]];
            $this->renumber($ordered->all());
        }

        return back();
    }

    /** Positions 0..n-1 in the given (or current) order. */
    private function renumber(?array $ids = null): void
    {
        $ids ??= Project::query()->ordered()->pluck('id')->all();

        DB::transaction(function () use ($ids) {
            foreach ($ids as $position => $id) {
                Project::whereKey($id)->update(['position' => $position]);
            }
        });
    }

    /** English title and body are required; other languages fall back to English when empty. */
    private function validated(Request $request): array
    {
        $rules = [];
        foreach (array_keys(config('site.locales')) as $locale) {
            $required = $locale === 'en' ? 'required' : 'nullable';
            $rules["title.{$locale}"] = [$required, 'string', 'max:255'];
            $rules["meta.{$locale}"] = ['nullable', 'string', 'max:255'];
            $rules["body.{$locale}"] = [$required, 'string', 'max:10000'];
        }

        $data = $request->validate($rules, [], ['title.en' => 'English title', 'body.en' => 'English text']);

        // Store every locale key, empty ones as "" so the shape stays the same.
        $out = [];
        foreach (Project::FIELDS as $field) {
            foreach (array_keys(config('site.locales')) as $locale) {
                $out[$field][$locale] = (string) ($data[$field][$locale] ?? '');
            }
        }

        return $out;
    }
}
