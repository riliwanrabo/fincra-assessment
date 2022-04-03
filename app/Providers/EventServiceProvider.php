<?php

namespace App\Providers;

use App\Events\UserCreated;
use App\Events\WalletCredited;
use App\Events\WalletDebited;
use App\Listeners\UserCreatedListener;
use App\Listeners\WalletCreditedListener;
use App\Listeners\WalletDebitedListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        UserCreated::class => [
            UserCreatedListener::class
        ],

        WalletDebited::class => [
            WalletDebitedListener::class
        ],

        WalletCredited::class => [
            WalletCreditedListener::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
