<?php

namespace App\Providers;

use App\Models\Post;
use App\Translation\DatabaseOverrideLoader;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Texts edited in the admin override the lang file defaults.
        $this->app->extend('translation.loader', fn ($files) => new DatabaseOverrideLoader($files));
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

        // The Blog link and RSS link only show once something is published.
        View::composer(['partials.header', 'partials.footer'], function ($view) {
            $view->with('hasPosts', once(fn () => Post::query()->published()->exists()));
        });
    }
}
