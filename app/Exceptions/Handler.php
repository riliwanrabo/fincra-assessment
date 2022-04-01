<?php

namespace App\Exceptions;

use Throwable;
use RuntimeException;
use Illuminate\Support\Arr;
use Illuminate\Database\QueryException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Profiler\Profile;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var string[]
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var string[]
     */
    protected $dontFlash = [
        'current_password',
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
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {

        if (!$request->is('api/*')) {
            return parent::render($request, $e);
        }

        if ($e instanceof RouteNotFoundException) {
            return response()->not_found();
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return response()->error('HTTP method not allowed for this action');
        }

        if ($e instanceof ValidationException) {
            return response()->validation_error($e->errors());
        }

        if ($e instanceof NotFoundHttpException) {
            return response()->error($e->getMessage());
        }

        if ($e instanceof QueryException) {
            return response()->error($e->getMessage());
        }

        if ($e instanceof NotAllowedException) {
            return response()->forbidden($e->getMessage());
        }

        if ($e instanceof UnAuthorizedException) {
            return response()->unauthorized($e->getMessage());
        }

        if ($e instanceof AuthorizationException) {
            return response()->unauthorized($e->getMessage());
        }

        if ($e instanceof ModelNotFoundException) {
            $model = str_replace('App\\Models\\', '', $e->getModel());
            return response()->not_found("$model Not found");
        }

        if ($e instanceof MisMatchException) {
            return response()->error($e->getMessage());
        }

        if ($e instanceof \Illuminate\Http\Client\ConnectionException) {
            return response()->json([
                'error' => $e->getMessage(),
            ], $e->getCode());
        }

        if ($e instanceof \GuzzleHttp\Exception\ConnectException) {
            return response()->json([
                'error' => $e->getMessage(),
            ], $e->getCode());
        }

        if ($e instanceof AuthenticationException) {
            return response()->error($e->getMessage());
        }

        if ($e instanceof RuntimeException) {
            return response()->error($e->getMessage());
        }

        if ($e instanceof \Spatie\Permission\Exceptions\RoleDoesNotExist) {
            return response()->error($e->getMessage());
        }

        return parent::prepareJsonResponse($request, $e);
    }
}
