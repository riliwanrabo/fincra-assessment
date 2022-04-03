<?php

namespace App\Http\Controllers\Api;

use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    public function __construct(protected AuthService $authService)
    {
    }

    public function __invoke(LoginRequest $request): JsonResponse
    {
        $login = $this->authService->login($request->validated());

        return response()->ok($login);
    }
}
