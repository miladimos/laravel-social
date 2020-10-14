<?php

namespace Miladimos\Social\Providers;

use Illuminate\Support\ServiceProvider;


class SocialServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . "/../../config/config.php", 'social');

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        if ($this->app->runningInConsole()) {

            $this->registerPublishes();

        }
    }

    private function registerPublishes()
    {
        $this->publishes([
            __DIR__ . '/../../config/config.php' => config_path('social.php')
        ], 'social-config');

    }
}
