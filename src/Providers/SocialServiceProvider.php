<?php

namespace Miladimos\Social\Providers;

use Illuminate\Support\ServiceProvider;
use Miladimos\Social\Console\Commands\InstallPackageCommand;
use Miladimos\Social\Social;

class SocialServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . "/../../config/social.php", 'social');

        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        $this->registerFacades();
    }

    public function boot()
    {

        if ($this->app->runningInConsole()) {

            $this->registerCommands();

            $this->registerConfig();

            $this->registerMigrations();
        }
    }

    private function registerFacades()
    {
        $this->app->bind('social', function ($app) {
            return new Social();
        }, true);
    }

    private function registerConfig()
    {
        $this->publishes([
            __DIR__ . '/../../config/social.php' => config_path('social.php')
        ], 'social-config');
    }

    private function registerCommands()
    {
        $this->commands([
            InstallPackageCommand::class,
        ]);
    }

    private function registerMigrations()
    {
        // if (!class_exists('CreateTagsTable')) {
        $this->publishes([
            __DIR__ . '/../../database/migrations/create_tags_table.php' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_tags_tables.php'),
        ], 'migrations');
        // }
    }
}
