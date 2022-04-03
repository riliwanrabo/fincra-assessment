<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Http\Controllers\Controller;

class LogoutController extends Controller
{
    public function __construct(protected AuthService $authService)
    {
        $this->middleware('auth:api');
    }

    public function __invoke(Request $request)
    {
        $this->authService->logout($request);

        return response()->ok(["message" => "Logged out"]);
    }
}
