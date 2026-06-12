<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return null;
            }
            return redirect()->route('login')->with('error', 'Please login to continue.');
        });

        $exceptions->renderable(function (AuthorizationException $e, $request) {
            if ($request->expectsJson()) {
                return null;
            }
            return redirect()->back()->with('error', 'Unauthorized action.');
        });

        $exceptions->renderable(function (HttpException $e, $request) {
            if ($request->expectsJson()) {
                return null;
            }
            if ($e->getStatusCode() === 403) {
                return redirect()->back()->with('error', 'Forbidden.');
            }
            if ($e->getStatusCode() >= 500) {
                return redirect()->back()->with('error', 'Server error. Please try again.');
            }
            return null;
        });

        $exceptions->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return null;
            }
            return response()->view('error-404', [], 404);
        });
    })->create();
