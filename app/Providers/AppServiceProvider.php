<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use App\Models\Setting;
use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Set default string length
        Schema::defaultStringLength(191);
        // Check no use Eager Loading in local
        Model::preventLazyLoading(! app()->isProduction());
        // Settings table
        if (Schema::hasTable('settings')) {
            foreach (Setting::all() as $setting) {
                Config::set('settings.'.$setting->key, $setting->value);
            }
        }
        // User Observer
        User::observe(UserObserver::class);
    }
}
