<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function show()
    {
        return UserResource::make(auth()->user());
    }

    public function transactions()
    {
        $transactions =  TransactionResource::collection(
            auth()->user()->transactions
        );

        return response()->ok([
            ...compact('transactions'),
            'message' => __("Retreived transactions")
        ]);
    }
}
