<?php

namespace WanaKin\Webcron;

use Illuminate\Support\ServiceProvider;
use function config_path;

class WebcronServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(WebcronScheduler::class, fn () => new WebcronScheduler);
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../resources/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->publishes([
            __DIR__ . '/../config/webcron.php' => config_path('webcron.php')
        ], 'webcron-config');
    }
}