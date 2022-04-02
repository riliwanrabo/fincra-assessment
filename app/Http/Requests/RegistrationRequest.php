<?php

namespace App\Http\Requests;

use App\Enums\Gender;
use App\Enums\RoleType;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
{
    private string $csvRoles;

    public function __construct()
    {
        $this->csvRoles = implode(',', collect(RoleType::cases())->pluck('value')->values()->all());
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'role' => ['required', 'in:' . $this->csvRoles],
            'first_name' => ['required',],
            'last_name' => ['required',],
            'email' => ['required', 'email', 'unique:users,email'],
            'gender' => [
                'sometimes', 'in:' . implode(',', collect(Gender::cases())->pluck('value')->values()->all())
            ],
            'avatar' => ['sometimes', 'url'],
            'password' => [
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
                'confirmed'
            ],
            'password_confirmation' => ['required']
        ];
    }

    public function messages()
    {
        return [
            'role.in' => 'Selected role must be one of ' . $this->csvRoles
        ];
    }
}
