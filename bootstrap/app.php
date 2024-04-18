<?php

use App\Http\Middleware\EnsureUserHasRole;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => EnsureUserHasRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->acceptsJson()) {
                return response()->json([
                    'status' => __('error'),
                    'message' => __('The resource you are trying to access can not be found on this server.'),
                ], Response::HTTP_NOT_FOUND);
            }
        });

        $exceptions->render(function (AuthenticationException|UnauthorizedHttpException $e, Request $request) {
            if ($request->acceptsJson()) {
                return response()->json([
                    'status' => __('error'),
                    'message' => $e->getMessage(),
                ], $e instanceof AuthenticationException ? Response::HTTP_UNAUTHORIZED : Response::HTTP_FORBIDDEN);
            }
        });

        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->acceptsJson()) {
                return response()->json([
                    'status' => __('error'),
                    'message' => $e?->getMessage() ?? __('An error occurred while processing your request.'),
                ], $e instanceof HttpException ? $e->getStatusCode() : Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        });

    })->create();
