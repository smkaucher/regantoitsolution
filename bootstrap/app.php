<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Public, read-only, throttled chat endpoint: a stale page must never break it with a 419.
        $middleware->validateCsrfTokens(except: ['ai-chat']);
        // The language cookie is a plain "en" / "bn", so it is read without decrypting.
        $middleware->encryptCookies(except: ['lang']);
        $middleware->web(append: [\App\Http\Middleware\Localize::class]);
        $middleware->alias(['admin' => \App\Http\Middleware\EnsureAdmin::class]);
        // There is no public login: anyone signed out (e.g. by a password change elsewhere) goes to the admin login.
        $middleware->redirectGuestsTo(fn () => route('admin.login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Admin: a save after the session timed out (419). Instead of "Page Expired", go back to the form with what
        // was typed. If the admin was signed out too, the text is kept and put back right after logging in again.
        // (Laravel has already turned the TokenMismatchException into an HTTP 419 exception by the time this runs.)
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, \Illuminate\Http\Request $request) {
            if (!$e->getPrevious() instanceof \Illuminate\Session\TokenMismatchException || !$request->is('admin', 'admin/*')) {
                return null;
            }
            $strip = function (array $in) use (&$strip) {
                $out = [];
                foreach ($in as $k => $v) {
                    if ($v instanceof \Illuminate\Http\UploadedFile) {
                        continue;
                    }
                    $out[$k] = is_array($v) ? $strip($v) : $v;
                }

                return $out;
            };
            $input = $strip($request->except(['_token', '_method', 'password', 'current_password', 'password_confirmation']));
            $back = url()->previous();
            $back = str_starts_with($back, url('/admin')) && !str_contains($back, '/admin/login') ? $back : route('admin.dashboard');

            if ($request->is('admin/login')) {
                return redirect()->route('admin.login')->withInput($request->only('email'))->with('expired', 'login');
            }
            if (!auth()->check()) {
                $request->session()->put('admin_draft', ['url' => $back, 'input' => $input]);

                return redirect()->route('admin.login')->with('expired', 'signed_out');
            }

            return redirect($back)->withInput($input)->with('expired', 'saved_nothing');
        });

        // Admin: photos over PHP's post_max_size. This happens before the session starts, so the message
        // travels in the URL (?upload_error=1, shown by the admin layout) instead of a flash.
        $exceptions->render(function (\Illuminate\Http\Exceptions\PostTooLargeException $e, \Illuminate\Http\Request $request) {
            if ($request->is('admin', 'admin/*')) {
                $back = url()->previous();
                $back = str_starts_with($back, url('/admin')) ? $back : route('admin.dashboard');

                return redirect($back . (str_contains($back, '?') ? '&' : '?') . 'upload_error=1');
            }
        });
    })->create();
