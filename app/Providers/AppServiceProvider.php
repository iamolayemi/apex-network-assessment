<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Enable mass assignment protection
        Model::unguard();
        Model::shouldBeStrict();

        // Set the default password rules
        Password::defaults(function () {
            return $this->app->isProduction() ? Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised() : Password::min(8);
        });

        // Set the default token expiration times
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
    }
}
