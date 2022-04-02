<?php

namespace App\Http\Controllers\Api;

use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegistrationRequest;

class RegistrationController extends Controller
{
    public function __construct(protected AuthService $authService)
    {
    }

    public function __invoke(RegistrationRequest $request): JsonResponse
    {
        $account = $this->authService->register($request->all());

        return response()->created($account);
    }
}
