<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        if (!$this->app->routesAreCached()) {
            Passport::routes();
        }

        Gate::define('view-user', function (\App\Models\User $user, $userId) {
            if ($user->hasRole('admin')) return true;
            return $userId === auth()->id();
        });

        Gate::define('do-wallet-transfer', function (\App\Models\User $user, $userId) {
            if ($user->hasRole('admin')) return true;
            return $userId === auth()->id();
        });
    }
}
