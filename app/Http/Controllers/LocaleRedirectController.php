<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Sends "/" to the best locale: the remembered cookie, then the browser's
 * Accept-Language, then the first configured locale.
 */
class LocaleRedirectController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $supported = array_keys(config('site.locales'));
        $locale = $request->cookie(SetLocale::COOKIE);

        if (! in_array($locale, $supported, true)) {
            $locale = $request->getPreferredLanguage($supported) ?? $supported[0];
        }

        return redirect()->route('home', ['locale' => $locale]);
    }
}
