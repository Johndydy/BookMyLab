<?php

namespace App\Providers;

use App\Models\Booking;
use App\Policies\BookingPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Booking::class => BookingPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Dynamic permission gates
        Gate::define('check-permission', function ($user, $permission) {
            return $user->hasPermission($permission);
        });

        Gate::define('check-role', function ($user, $role) {
            return $user->hasRole($role);
        });

        // Specific permission gates for common operations
        Gate::define('approve-bookings', function ($user) {
            return $user->hasPermission('approve-booking');
        });

        Gate::define('manage-labs', function ($user) {
            return $user->hasPermission('manage-laboratory');
        });

        Gate::define('manage-equipment', function ($user) {
            return $user->hasPermission('manage-equipment');
        });

        Gate::define('view-reports', function ($user) {
            return $user->hasPermission('view-reports');
        });
    }
}
