<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Support\Markdown;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function index(): Response
    {
        $posts = Post::query()
            ->orderByRaw('published_at is null desc') // drafts first
            ->orderByDesc('published_at')->orderByDesc('id')
            ->get(['id', 'slug', 'title', 'locale', 'published_at', 'updated_at'])
            ->map(fn (Post $post) => [
                ...$post->only('id', 'slug', 'title', 'locale'),
                'status' => $post->status(),
                'published_at' => $post->published_at?->toIso8601String(),
                'url' => $post->status() === 'published' ? route('blog.show', ['locale' => $post->locale, 'slug' => $post->slug]) : null,
            ]);

        return Inertia::render('Admin/Posts/Index', ['posts' => $posts]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Posts/Edit', ['post' => null, 'locales' => config('site.locales')]);
    }

    public function store(Request $request): RedirectResponse
    {
        $post = Post::create($this->validated($request));

        return redirect()->route('admin.posts.edit', $post)->with('status', "Saved \"{$post->title}\" ({$post->status()}).");
    }

    public function edit(Post $post): Response
    {
        return Inertia::render('Admin/Posts/Edit', [
            'post' => [
                ...$post->only('id', 'slug', 'title', 'excerpt', 'body', 'locale'),
                'status' => $post->status(),
                'published_at' => $post->published_at?->toIso8601String(),
                'url' => route('blog.show', ['locale' => $post->locale, 'slug' => $post->slug]),
            ],
            'locales' => config('site.locales'),
        ]);
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $post->update($this->validated($request, $post));

        return back()->with('status', "Saved \"{$post->title}\" ({$post->status()}).");
    }

    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();

        return redirect()->route('admin.posts')->with('status', "Deleted \"{$post->title}\".");
    }

    /** Renders Markdown for the editor's preview tab, exactly as the site will. */
    public function preview(Request $request): JsonResponse
    {
        $body = $request->validate(['body' => ['nullable', 'string', 'max:200000']])['body'] ?? '';

        return response()->json(['html' => Markdown::render($body)]);
    }

    private function validated(Request $request, ?Post $post = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'regex:'.Post::SLUG_FORMAT, Rule::unique('posts', 'slug')->ignore($post)],
            'locale' => ['required', Rule::in(array_keys(config('site.locales')))],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string', 'max:200000'],
            'published_at' => ['nullable', 'date'],
        ], [
            'slug.regex' => 'The slug may only contain lowercase letters, numbers and single dashes.',
        ]);

        // The editor sends ISO 8601 with the browser's offset; store in the app timezone.
        $data['published_at'] = isset($data['published_at'])
            ? Carbon::parse($data['published_at'])->setTimezone(config('app.timezone'))
            : null;

        return $data;
    }
}
