<?php

namespace App\Providers;

use App\Integration\Whatsapp\Whatsapp;
use App\Models\NotificationChannel;
use App\Observers\NotificationChannelObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(Whatsapp::class, fn () => new Whatsapp(config('services.waha.api_key')));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(! $this->app->isProduction());

        NotificationChannel::observe(NotificationChannelObserver::class);
    }
}
