<?php

namespace App\Http\Middleware;

use App\Support\Translator;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Picks the visitor's language (English or Bangla, remembered in the "lang" cookie) and, for Bangla,
 * translates the public pages on the way out. The admin panel stays in English.
 */
class Localize
{
    public function handle(Request $request, Closure $next): Response
    {
        $bn = $request->cookie('lang') === 'bn' && !$request->is('admin', 'admin/*', 'lang/*');
        app()->setLocale($bn ? 'bn' : 'en');

        $response = $next($request);

        if ($bn && !$response->isRedirection()
            && str_contains((string) $response->headers->get('Content-Type'), 'text/html')) {
            $response->setContent(Translator::html((string) $response->getContent()));
        }

        return $response->header('Vary', 'Cookie');
    }
}
