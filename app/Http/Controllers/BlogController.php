<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        $years = Post::query()->published()->newestFirst()
            ->get(['id', 'slug', 'title', 'excerpt', 'locale', 'published_at'])
            ->groupBy(fn (Post $post) => $post->published_at->year);

        return view('blog.index', ['years' => $years]);
    }

    public function show(string $slug): View
    {
        $post = Post::query()->published()->where('slug', $slug)->firstOrFail();

        return view('blog.show', [
            'post' => $post,
            'older' => $post->older(),
            'newer' => $post->newer(),
        ]);
    }
}
