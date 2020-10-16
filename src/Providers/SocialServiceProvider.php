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


        if (! class_exists('CreatePostsTable')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/create_posts_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_posts_table.php'),
                // you can add any number of migrations here
            ], 'migrations');
        }

    }
}
