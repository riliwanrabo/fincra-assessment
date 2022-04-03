<?php

namespace App\Services;

use App\Models\User;
use App\Enums\RoleType;
use App\Events\UserCreated;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserResource;
use App\Exceptions\UnAuthorizedException;
use Illuminate\Console\Scheduling\Event;

class AuthService
{
    /**
     * Login action
     *
     * @param array $payload
     * @return array
     */
    public function login(array $payload): array
    {
        if (!auth()->attempt($payload)) {
            throw new UnAuthorizedException(__("Invalid credentials"));
        }

        $user = auth()->user();

        $token =  $user->createToken(env('APP_NAME'))->accessToken;

        return [
            'user' => UserResource::make($user),
            'token' => $token,
        ];
    }

    public function register(array $payload): array
    {
        $user = DB::transaction(function () use ($payload) {

            $payload['password'] = bcrypt($payload['password']);

            $user = User::create($payload);

            $role = \App\Models\Role::whereName($payload['role'])->first();

            $user->assignRole($role);

            return $user;
        }, 5);

        event(new UserCreated($user));

        return [
            'user' => UserResource::make($user),
            'message' => 'Account registered'
        ];
    }

    public function logout()
    {
        request()->user()->token()->revoke();
    }
}
