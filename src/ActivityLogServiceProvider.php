<?php

namespace AlMosabbirRakib\ActivityLog;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class ActivityLogServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPublishing();
        $this->registerResources();
        $this->registerRoutes();
        $this->registerCommands();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/activity-log.php',
            'activity-log'
        );

        $this->app->singleton('activity-log', function ($app) {
            return new ActivityLogger();
        });
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            // Publish config
            $this->publishes([
                __DIR__ . '/config/activity-log.php' => config_path('activity-log.php'),
            ], 'activity-log-config');

            // Publish migrations
            $this->publishes([
                __DIR__ . '/database/migrations/create_activity_logs_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_activity_logs_table.php'),
            ], 'activity-log-migrations');

            // Publish views
            $this->publishes([
                __DIR__ . '/resources/views' => resource_path('views/vendor/activity-log'),
            ], 'activity-log-views');

            // Publish Vue components
            $this->publishes([
                __DIR__ . '/resources/js/components' => resource_path('js/components/activity-log'),
            ], 'activity-log-components');

            // Publish assets
            $this->publishes([
                __DIR__ . '/resources/assets' => public_path('vendor/activity-log'),
            ], 'activity-log-assets');
        }
    }

    /**
     * Register the package resources.
     *
     * @return void
     */
    protected function registerResources()
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'activity-log');
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    protected function registerRoutes()
    {
        if (!config('activity-log.routes.enabled', true)) {
            return;
        }

        Route::group($this->webRouteConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        });

        Route::group($this->apiRouteConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
        });
    }

    /**
     * Get the web route group configuration array.
     *
     * @return array
     */
    protected function webRouteConfiguration()
    {
        return [
            'prefix' => config('activity-log.routes.prefix', 'activity-logs'),
            'middleware' => config('activity-log.routes.middleware', ['web', 'auth']),
        ];
    }

    /**
     * Get the API route group configuration array.
     *
     * @return array
     */
    protected function apiRouteConfiguration()
    {
        return [
            'prefix' => 'api/' . config('activity-log.routes.prefix', 'activity-logs'),
            'middleware' => config('activity-log.routes.api_middleware', ['api', 'auth:sanctum']),
        ];
    }

    /**
     * Register the package's commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\Commands\CleanActivityLogsCommand::class,
                Console\Commands\InstallCommand::class,
            ]);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['activity-log'];
    }
}

