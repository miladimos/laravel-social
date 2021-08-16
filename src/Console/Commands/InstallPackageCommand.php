<?php


namespace Miladimos\Social\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallPackageCommand extends Command
{
    protected $signature = 'social:install';

    protected $description = 'Install the Social package';

    public function handle()
    {
        $this->line("\t... Welcome To Social Package Installer ...");

        //config
        if (File::exists(config_path('social.php'))) {
            $confirm = $this->confirm("social.php already exist. Do you want to overwrite?");
            if ($confirm) {
                $this->publishConfig();
            } else {
                $this->error("you must overwrite config file");
                exit;
            }
        } else {
            $this->publishConfig();
        }

    //    $this->publishMigration();
    //    $this->info("migrations published.");


        $this->info("Social Package Successfully Installed. Star me on Github :) \n");
        $this->info("\t\tGood Luck.");
    }

    private function publishConfig()
    {
        $this->call('vendor:publish', [
            '--provider' => "Miladimos\Social\Providers\SocialServiceProvider",
            '--tag' => 'social-config',
            '--force' => true
        ]);
    }

    private function publishMigration()
    {
        $this->call('vendor:publish', [
            '--provider' => "Miladimos\Social\Providers\SocialServiceProvider",
            '--tag' => 'migrations',
            '--force' => true
        ]);
    }

    //     //assets
    //     if (File::exists(public_path('social'))) {
    //         $confirm = $this->confirm("social directory already exist. Do you want to overwrite?");
    //         if ($confirm) {
    //             $this->publishAssets();
    //             $this->info("assets overwrite finished");
    //         } else {
    //             $this->info("skipped assets publish");
    //         }
    //     } else {
    //         $this->publishAssets();
    //         $this->info("assets published");
    //     }

    //     //migration
    //     if (File::exists(database_path("migrations/$migrationFile"))) {
    //         $confirm = $this->confirm("migration file already exist. Do you want to overwrite?");
    //         if ($confirm) {
    //             $this->publishMigration();
    //             $this->info("migration overwrite finished");
    //         } else {
    //             $this->info("skipped migration publish");
    //         }
    //     } else {
    //         $this->publishMigration();
    //         $this->info("migration published");
    //     }
    //     $this->call('migrate');
    // }

    // private function publishAssets()
    // {
    //     $this->call('vendor:publish', [
    //         '--provider' => "Miladimos\Social\Providers\SocialServiceProvider",
    //         '--tag'      => 'assets',
    //         '--force'    => true
    //     ]);
    // }

}
