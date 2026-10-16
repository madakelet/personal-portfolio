<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('home', [
            'projects' => Project::query()->ordered()->get(),
        ]);
    }
}
