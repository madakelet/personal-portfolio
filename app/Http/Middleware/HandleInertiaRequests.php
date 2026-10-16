<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

/**
 * Inertia is only used for the admin. The public site is plain Blade.
 */
class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'admin';

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user()?->only('name', 'email'),
            ],
            'flash' => [
                'status' => fn () => $request->session()->get('status'),
            ],
            'siteUrl' => route('root'),
        ];
    }
}
