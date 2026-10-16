<?php

use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\TextController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocaleRedirectController;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

Route::get('/', LocaleRedirectController::class)->name('root');
Route::get('feed.xml', FeedController::class)->name('feed');

Route::prefix('{locale}')
    ->where(['locale' => implode('|', array_keys(config('site.locales')))])
    ->middleware(SetLocale::class)
    ->group(function () {
        Route::get('/', HomeController::class)->name('home');
        Route::get('blog', [BlogController::class, 'index'])->name('blog.index');
        Route::get('blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
    });

// Admin (Inertia + Vue). Login and logout routes come from Fortify.
Route::prefix('admin')
    ->middleware(['auth', HandleInertiaRequests::class])
    ->group(function () {
        Route::inertia('/', 'Admin/Home')->name('admin');
        Route::get('texts', [TextController::class, 'index'])->name('admin.texts');
        Route::put('texts', [TextController::class, 'update'])->name('admin.texts.update');
        Route::delete('texts', [TextController::class, 'destroy'])->name('admin.texts.reset');

        Route::get('projects', [ProjectController::class, 'index'])->name('admin.projects');
        Route::post('projects', [ProjectController::class, 'store'])->name('admin.projects.store');
        Route::put('projects/{project}', [ProjectController::class, 'update'])->name('admin.projects.update');
        Route::delete('projects/{project}', [ProjectController::class, 'destroy'])->name('admin.projects.destroy');
        Route::post('projects/{project}/move', [ProjectController::class, 'move'])->name('admin.projects.move');

        Route::get('posts', [PostController::class, 'index'])->name('admin.posts');
        Route::get('posts/create', [PostController::class, 'create'])->name('admin.posts.create');
        Route::post('posts', [PostController::class, 'store'])->name('admin.posts.store');
        Route::post('posts/preview', [PostController::class, 'preview'])->name('admin.posts.preview');
        Route::get('posts/{post}/edit', [PostController::class, 'edit'])->name('admin.posts.edit');
        Route::put('posts/{post}', [PostController::class, 'update'])->name('admin.posts.update');
        Route::delete('posts/{post}', [PostController::class, 'destroy'])->name('admin.posts.destroy');
    });
