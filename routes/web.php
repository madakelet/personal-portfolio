<?php

use App\Http\Controllers\LocaleRedirectController;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

Route::get('/', LocaleRedirectController::class)->name('root');

Route::prefix('{locale}')
    ->where(['locale' => implode('|', array_keys(config('site.locales')))])
    ->middleware(SetLocale::class)
    ->group(function () {
        Route::view('/', 'home')->name('home');
    });

// Admin (Inertia + Vue). Login and logout routes come from Fortify.
Route::prefix('admin')
    ->middleware(['auth', HandleInertiaRequests::class])
    ->group(function () {
        Route::inertia('/', 'Admin/Home')->name('admin');
    });
