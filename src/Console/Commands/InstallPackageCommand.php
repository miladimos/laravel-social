<?php


namespace Miladimos\Social\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallPackageCommand extends Command
{
    protected $signature = 'social:install';

    protected $description = 'Install the social package';

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

        if (!empty(File::glob(database_path('migrations\*_create_socials_table.php')))) {
            $list  = File::glob(database_path('migrations\*_create_socials_table.php'));
            collect($list)->each(function ($item) {
                File::delete($item);
                $this->warn("Deleted: " . $item);
            });
            $this->publishMigration();
        } else {
            $this->publishMigration();
        }

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
            '--tag' => 'social-migrations',
            '--force' => true
        ]);
    }
}
