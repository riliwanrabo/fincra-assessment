<?php

namespace App\Providers;

use Barryvdh\LaravelIdeHelper\Macro;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\Response as StatusCode;
use Illuminate\Support\Facades\Response;

/**
 * Class ResponseMacroProvider
 * @package App\Providers
 * @mixin Macro
 */
class ResponseMacroProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Response macros

        Response::macro('ok', fn (object|array $data, $headers = []) => Response::json($data, StatusCode::HTTP_OK, $headers));

        Response::macro('created', fn ($data, $headers = []) => Response::json($data, StatusCode::HTTP_CREATED, $headers));

        Response::macro('no_content', fn ($headers = []) => Response::noContent());

        Response::macro('error', fn ($error, $headers = []) => Response::json(['message' => $error], StatusCode::HTTP_BAD_REQUEST, $headers));

        Response::macro('validation_error', fn ($errors, $message = 'There are validation errors', $headers = []) =>
        Response::json([
            'errors' => $errors,
            'message' => $message,
        ], StatusCode::HTTP_BAD_REQUEST, $headers));

        Response::macro(
            'unauthorized',
            fn ($error, $headers = []) => Response::json(['message' => $error], StatusCode::HTTP_UNAUTHORIZED, $headers)
        );

        Response::macro(
            'not_allowed',
            fn ($error, $headers = []) => Response::json(['message' => $error], StatusCode::HTTP_METHOD_NOT_ALLOWED, $headers)
        );

        Response::macro(
            'payment_required',
            fn ($error, $headers = []) => Response::json(['message' => $error], StatusCode::HTTP_PAYMENT_REQUIRED, $headers)
        );

        Response::macro(
            'upx_entity',
            fn ($error, $headers = []) => Response::json(['message' => $error], StatusCode::HTTP_UNPROCESSABLE_ENTITY, $headers)
        );

        Response::macro('forbidden', fn ($error, $headers = []) => Response::json(['message' => $error], StatusCode::HTTP_FORBIDDEN, $headers));

        Response::macro(
            'found',
            fn ($data = null, $headers = []) => Response::json([
                'message' => $data ?? 'Resource Found'
            ], StatusCode::HTTP_FOUND, $headers)
        );

        Response::macro(
            'not_found',
            fn ($data = null, $headers = []) => Response::json([
                'message' => $data ?? 'Resource not found'
            ], StatusCode::HTTP_NOT_FOUND, $headers)
        );

        Response::macro(
            'server_error',
            fn ($error, $headers = []) => Response::json(['message' => $error], StatusCode::HTTP_INTERNAL_SERVER_ERROR, $headers)
        );
    }
}
