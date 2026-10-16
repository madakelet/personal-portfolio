<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Project;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('home', [
            'projects' => Project::query()->ordered()->get(),
            'posts' => Post::query()->published()->newestFirst()->limit(4)->get(['id', 'slug', 'title', 'locale', 'published_at']),
        ]);
    }
}
