<?php

namespace App\Providers;

use App\Grants\PasswordCustomProvider;
use App\Grants\RefreshTokenCustom;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;
use Laravel\Passport\Bridge\RefreshTokenRepository;
use League\OAuth2\Server\AuthorizationServer;
use Carbon\Carbon;
use Illuminate\Contracts\Hashing\Hasher;
use DateInterval;
use DateTimeInterface;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
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
        app(AuthorizationServer::class)->enableGrantType(
            $this->makePasswordGrantCustomProvider(), $this->setTokensExpireIn()
        );
        app(AuthorizationServer::class)->enableGrantType(
            $this->makeRefreshTokenCustomProvider(), $this->setTokensExpireIn()
        );
        Passport::routes();
        Passport::tokensExpireIn(Carbon::now()->addYear());
        Passport::refreshTokensExpireIn(Carbon::now()->addYear());
    }

    /**
     * Password grant with custom provider
     */
    protected function makePasswordGrantCustomProvider()
    {
        $grant = new PasswordCustomProvider(
            $this->app->make(RefreshTokenRepository::class),
            $this->app->make(Hasher::class)
        );

        $grant->setRefreshTokenTTL($this->setTokensExpireIn());

        return $grant;
    }

    /**
     * Refresh token custom 
     */
    protected function makeRefreshTokenCustomProvider()
    {
        $grant = new RefreshTokenCustom(
            $this->app->make(RefreshTokenRepository::class)
        );

        $grant->setRefreshTokenTTL($this->setTokensExpireIn());

        return $grant;
    }

    /**
     * Set token expire
     */
    protected function setTokensExpireIn()
    {
        return new DateInterval('P1D');
    }
}
