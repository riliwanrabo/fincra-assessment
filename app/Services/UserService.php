<?php

namespace App\Services;

use App\Events\UserCreated;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;

class UserService
{
    public function __construct()
    {
    }

    public function fetchUsers(string $role = null)
    {
        $users = User::query();

        request()->whenFilled(
            'status',
            fn () =>
            $users->whereHas('status', fn ($status) => $status->whereName(
                request()->get('status')
            ))
        );

        request()->whenFilled(
            'type',
            fn () =>
            $users->whereHas('roles', fn ($roles) => $roles->whereName(
                request()->get('type')
            ))
        );

        return ($users);
    }

    public function fetchUser(User $user)
    {
        return UserResource::make($user);
    }

    public function createUser(string $roleType, array $payload)
    {
        $payload['password'] = bcrypt(
            str()->substr($payload['phone'], 0, 8)
        );

        $user = User::firstOrCreate($payload);

        $role = Role::whereName($roleType)->first();

        $user->assignRole($role);

        event(new UserCreated($user));

        return $user;
    }

    public function updateUser(User $user, array $payload)
    {
        $user->update($payload);

        return $user->refresh();
    }

    public function fundWallet(User $user)
    {
    }
}
