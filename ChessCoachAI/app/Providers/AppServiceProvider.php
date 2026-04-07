<?php

namespace App\Providers;

use App\Services\GeminiService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Singleton = one instance per request lifecycle
        // equivalent to builder.Services.AddSingleton<GeminiService>() in ASP.NET
        $this->app->singleton(GeminiService::class);
    }

    public function boot(): void
    {
        //
    }
}
