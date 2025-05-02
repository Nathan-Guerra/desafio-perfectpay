<?php

namespace App\Providers;

use GuzzleHttp\Client;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('insecure-http-client', function (Application $app) {
            return new Client(['verify' => false, 'http_errors' => false]);
        });
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::bind('paymentUuid', function ($value) {
            return \App\Models\Payment::query()->where('payment_uuid', $value)->firstOrFail();
        });
    }
}
