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
     * @var array
     */
    protected $policies = [
         'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('superAdmin', function($user){
            return $user->role === 'super_admin';
        });

        Gate::define('hotelReceptionist', function($user){
            return $user->role === 'hotel_receptionist';
        });

        Gate::define('hotelOwner', function($user){
            return $user->role === 'hotel_owner';
        });

        Passport::routes();

        //
    }
}
