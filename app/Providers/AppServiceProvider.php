<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // The CV PDF is optional: links to it only render once the file exists.
        View::composer(['home', 'partials.header'], function ($view) {
            $path = config('site.cv_pdf');
            $view->with('cvUrl', file_exists(public_path($path)) ? asset($path) : null);
        });
    }
}
