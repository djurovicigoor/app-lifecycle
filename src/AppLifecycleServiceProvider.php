<?php

namespace Djurovicigoor\AppLifecycle;

use Illuminate\Support\ServiceProvider;
use Djurovicigoor\AppLifecycle\Commands\CopyAssetsCommand;

class AppLifecycleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AppLifecycle::class, function () {
            return new AppLifecycle();
        });
    }

    public function boot(): void
    {
        // Register plugin hook commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                CopyAssetsCommand::class,
            ]);
        }
    }
}