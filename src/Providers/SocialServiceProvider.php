<?php

namespace Miladimos\Social\Providers;

use Illuminate\Support\ServiceProvider;
use Miladimos\Social\Social;

class SocialServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . "/../../config/social.php", 'social');

        $this->registerFacades();
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        if ($this->app->runningInConsole()) {

            $this->registerConfig();

            $this->registerMigrations();
        }
    }

    private function registerFacades()
    {
        $this->app->bind('social', function ($app) {
            return new Social();
        });
    }

    private function registerConfig()
    {

        $this->publishes([
            __DIR__ . '/../../config/config.php' => config_path('social.php')
        ], 'social-config');
    }

    private function registerMigrations()
    {

        if (!class_exists('CreateLikesTable')) {
            $this->publishes([
                __DIR__ . '/../../database/migrations/create_likes_table.php' => database_path('migrations'),
            ], 'migrations');
        }

        if (!class_exists('CreateBookmarksTable')) {
            $this->publishes([
                __DIR__ . '/../../database/migrations/create_bookmarks_table.php' => database_path('migrations'),
            ], 'migrations');
        }

        if (!class_exists('CreateCategoriesTable')) {
            $this->publishes([
                __DIR__ . '/../../database/migrations/create_categories_table.php' => database_path('migrations'),
            ], 'migrations');
        }

        if (!class_exists('CreateFollowsTable')) {
            $this->publishes([
                __DIR__ . '/../../database/migrations/create_follows_table.php' => database_path('migrations'),
            ], 'migrations');
        }

        if (!class_exists('CreateLikeCountersTable')) {
            $this->publishes([
                __DIR__ . '/../../database/migrations/create_like_counters_table.php' => database_path('migrations'),
            ], 'migrations');
        }

        if (!class_exists('CreateSubscriptionTable')) {
            $this->publishes([
                __DIR__ . '/../../database/migrations/create_subscriptions_table.php' => database_path('migrations'),
            ], 'migrations');
        }

        if (!class_exists('CreateTagsTable')) {
            $this->publishes([
                __DIR__ . '/../../database/migrations/create_tags_table.php' => database_path('migrations'),
            ], 'migrations');
        }
    }
}
