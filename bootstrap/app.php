<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Render terminates TLS at its edge and forwards plain HTTP to the
        // container, so without this, Laravel thinks every request is
        // insecure — route()/url() then generate http:// URLs even though
        // the site is served over https://, which browsers block as mixed
        // content for fetch()/XHR calls (this is what broke the dunk
        // archive: it's the only place in the app that builds a fetch()
        // URL with route() instead of a hand-written relative path).
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
