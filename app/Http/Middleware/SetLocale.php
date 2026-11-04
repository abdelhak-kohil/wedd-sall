<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = Session::get('locale');

        if (!$locale && auth()->check()) {
            $locale = auth()->user()->preferred_language;
        }

        if (!$locale || !in_array($locale, ['fr', 'ar'], true)) {
            $locale = 'fr';
        }

        App::setLocale($locale);

        return $next($request);
    }
}
