<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Response;

/** RSS 2.0 feed of the 20 newest published posts, with full content. */
class FeedController extends Controller
{
    public function __invoke(): Response
    {
        $posts = Post::query()->published()->newestFirst()->limit(20)->get();

        return response()
            ->view('feed', ['posts' => $posts], 200)
            ->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }
}
