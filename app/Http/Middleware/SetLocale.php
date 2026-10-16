<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applies the {locale} route prefix: sets the app and date locale, makes
 * route() fill in the current locale by default, and remembers the choice
 * in a cookie so the bare "/" can redirect back to it.
 */
class SetLocale
{
    public const COOKIE = 'locale';

    public function handle(Request $request, Closure $next): Response
    {
        $locale = (string) $request->route('locale');

        App::setLocale($locale);
        Carbon::setLocale($locale);
        URL::defaults(['locale' => $locale]);

        // Controllers and views don't need the prefix as a parameter.
        $request->route()->forgetParameter('locale');

        $response = $next($request);

        if ($request->cookie(self::COOKIE) !== $locale) {
            $response->headers->setCookie(Cookie::create(self::COOKIE, $locale, now()->addYear(), sameSite: 'lax'));
        }

        return $response;
    }
}
