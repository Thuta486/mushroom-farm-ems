<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    private const SUPPORTED_LOCALES = ['en', 'my'];

    public function update(Request $request, string $locale): RedirectResponse
    {
        if (in_array($locale, self::SUPPORTED_LOCALES, true)) {
            $request->session()->put('locale', $locale);
        }

        return back();
    }
}
