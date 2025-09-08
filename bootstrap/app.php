<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            if ($request->is('api/*')) {
                return true;
            }

            return $request->expectsJson();
        });

        $exceptions->render(function (AuthorizationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to access this resource.',
                ], Response::HTTP_FORBIDDEN);
            } else {
                return parent::render($request, $e);
            }
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found.',
                    'error' => $e->getMessage(),
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([ // return for web-based
                'success' => false,
                'message' => $e->getMessage(),
                'error' => 'An unexpected error occurred.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        });

        $exceptions->render(function (QueryException $e, Request $request) {
            if ($e->getCode() == '23505') { // postgre exception for unique key
                if (preg_match('/Key \((.+?)\)=\((.+?)\)/', $e->getMessage(), $matches)) {
                    $field = ucwords(str_replace('_', ' ', $matches[1]));
                    $value = $matches[2];

                    return response()->json([
                        'success' => false,
                        'message' => "$field ($value) already exists.",
                        'errors' => $e->getMessage(),
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                }

                // Fallback if regex fails but we still catch _unique
                if (strpos($e->getMessage(), '_unique') !== false) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Duplicate entry already exists.',
                        'errors' => $e->getMessage(),
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                }
            }
        });
    })->create();
