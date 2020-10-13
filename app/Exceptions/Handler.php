<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Laravel\Passport\Exceptions\InvalidAuthTokenException;
use Laravel\Passport\Exceptions\MissingScopeException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        //
    }
    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $request->expectsJson()
            ? response()->json(['message' => $exception->getMessage()], 401)
            : response()->json(['message' => $exception->getMessage()], 401); // ここを変更する
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof AccessDeniedHttpException || $exception instanceof MissingScopeException) {
            return response()->json(['message' => 'Access Denied!'], 403);
        }
        if ($exception instanceof ModelNotFoundException && $request->wantsJson()) {
            return response()->json(['message' => 'Resource Not Found!'], 404);
        }

        if($exception instanceof InvalidAuthTokenException && $request->wantsJson()) {
            return response()->json(['message' => 'Token Expired'], 401);
        }
        if($exception instanceof NotFoundHttpException && $request->wantsJson()) {
            return response()->json(['message' => 'Url Not Found!'], 404);
        }
        if($exception instanceof AuthorizationException && $request->wantsJson()) {
            return response()->json(['message' => 'This action is unauthorized!'], 403);
        }
        return parent::render($request, $exception);
    }
}
