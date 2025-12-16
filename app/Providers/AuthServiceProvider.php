<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        
        // Di Laravel 12, Passport::routes() sudah tidak dibutuhkan lagi
        // Routes akan otomatis didaftarkan oleh Passport Service Provider
        
        // Konfigurasi token expiration
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addDays(15));
        
        // Optional: Load keys dari storage path (jika diperlukan)
        // Passport::loadKeysFrom(__DIR__.'/../../storage/oauth-public.key');
        
        // Optional: Hashids untuk client IDs (jika diperlukan)
        // Passport::hashClientSecrets();
        
        // Optional: Enable implicit grant (tidak disarankan untuk production)
        // Passport::enableImplicitGrant();
    }
}
