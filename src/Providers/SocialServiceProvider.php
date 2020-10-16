<?php

namespace Miladimos\Social\Providers;

use Illuminate\Support\ServiceProvider;
use Miladimos\Social\Social;


class SocialServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . "/../../config/config.php", 'social');

        $this->registerFacades();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        if ($this->app->runningInConsole()) {

            $this->registerPublishes();

        }
    }

    private function registerFacades()
    {
        $this->app->bind('social', function ($app) {
            return new Social();
        });
    }

    private function registerPublishes()
    {

        $this->publishes([
            __DIR__ . '/../../config/config.php' => config_path('social.php')
        ], 'social-config');

        if (! class_exists('CreateLikesTable')) {
            $this->publishes([
                __DIR__ . '/../../database/migrations/2020_10_16_162735_create_likes_table.php' => database_path('migrations'),
            ], 'migrations');
        }

    }
}
