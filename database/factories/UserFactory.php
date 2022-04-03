<?php

namespace Database\Factories;

use App\Models\User;
use App\Enums\RoleType;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    private $domain = 'fincra.test';
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        static $count = 1;

        $email = "user" . $count++ . "@$this->domain";

        $userExists = User::whereEmail($email)->exists();

        if ($userExists) {
            $count = User::count() + 1;
            $email = "user" . $count . "@$this->domain";
        }

        return [
            'avatar' => 'https://picsum.photos/1000/650?random=' . Str::random(3),
            'email' => $email,
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'password' => bcrypt('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }

    public function configure()
    {
        return $this->afterCreating(function (User $user) {
            $role = \App\Models\Role::whereName(RoleType::AGENT->value)->first();
            $user->assignRole($role);
        });
    }
}
