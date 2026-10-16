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

        // The RSS links only show once something is published.
        View::composer(['partials.footer', 'partials.head-meta'], function ($view) {
            // One query per request, shared by both partials.
            $attributes = request()->attributes;
            if (! $attributes->has('hasPosts')) {
                $attributes->set('hasPosts', Post::query()->published()->exists());
            }
            $view->with('hasPosts', $attributes->get('hasPosts'));
        });
    }
}
