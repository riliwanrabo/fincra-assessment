<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Enums\RoleType;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function __construct(protected UserService $userService)
    {
        $this->middleware('auth:api');
        $this->middleware('role:admin')->only('index', 'store', 'update', 'destroy');
    }

    public function index()
    {
        $users =  UserResource::collection($this->userService->fetchUsers()->paginate());

        return $users;
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => ['required'],
            'last_name' => ['required'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['required', 'string', 'min:11'],
            'role' => ['required', 'in:admin,agent']
        ]);

        $user = $this->userService->createUser($request->role, $request->except('role'));

        return response()->created([
            ...compact('user'),
            "message" => __("User created")
        ]);
    }

    public function show(User $user)
    {
        if (!Gate::allows('view-user', $user->id)) {
            return response()->forbidden(__("UnAuthorized"));
        }
        return response()->ok([
            'user' => UserResource::make($user),
            'message' => "User record retrieved"
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'first_name' => ['sometimes'],
            'last_name' => ['sometimes'],
            'email' => ['sometimes', 'email', 'unique:users,email,' . $request->user_id],
        ]);

        $user = User::findOrFail($request->user_id);

        $this->userService->updateUser($user, $request->all());

        return response()->ok([
            "message" => __("User updated")
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->no_content();
    }
}
