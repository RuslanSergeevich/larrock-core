<?php

namespace Larrock\Core;

use Illuminate\Support\ServiceProvider;
use Larrock\Core\Commands\LarrockAssetsCommand;
use Larrock\Core\Commands\LarrockCheckCommand;
use Larrock\Core\Commands\LarrockInstallCommand;
use Larrock\Core\Commands\LarrockManagerCommand;
use Larrock\Core\Commands\LarrockRenamePublicDirectoryCommand;
use Larrock\Core\Commands\LarrockUpdateEnvCommand;
use Larrock\Core\Commands\LarrockUpdateVendorConfigCommand;
use Larrock\Core\Middleware\AdminMenu;
use Larrock\Core\Middleware\SaveAdminPluginsData;
use Spatie\MediaLibrary\Filesystem\DefaultFilesystem;
use Larrock\Core\Helpers\MediaFilesystem;

class LarrockCoreServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->loadViewsFrom(__DIR__.'/views', 'larrock');
        $this->loadTranslationsFrom(__DIR__.'/lang', 'larrock');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        $public_path = public_path();
        if(substr($public_path, -6) === 'public'){
            $public_path .= '_html';
        }

        $this->publishes([
            __DIR__.'/lang' => resource_path('lang/vendor/larrock')
        ], 'lang');
        $this->publishes([
            __DIR__.'/views/front' => base_path('resources/views/vendor/larrock/front')
        ], 'views-front-core');
        $this->publishes([
            __DIR__.'/views/admin' => base_path('resources/views/vendor/larrock/admin')
        ], 'views-admin-core');
        $this->publishes([
            __DIR__.'/assets/public_html' => $public_path,
            __DIR__.'/assets/gulpfile' => base_path(),
            __DIR__.'/assets/bower' => base_path(),
        ], 'assets');
        $this->publishes([
            __DIR__.'/assets/policy' => $public_path
        ], 'doc');
        $this->publishes([
            __DIR__.'/config/larrock-core-adminmenu.php' => config_path('larrock-core-adminmenu.php'),
            __DIR__.'/config/larrock-sitemap.php' => config_path('larrock-sitemap.php'),
            __DIR__.'/config/larrock-to-dashboard.php' => config_path('larrock-to-dashboard.php'),
            __DIR__.'/config/larrock.php' => config_path('larrock.php'),
        ], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app['router']->aliasMiddleware('LarrockAdminMenu', AdminMenu::class);
        $this->app['router']->aliasMiddleware('SaveAdminPluginsData', SaveAdminPluginsData::class);

        $this->mergeConfigFrom( __DIR__.'/config/larrock-core-adminmenu.php', 'larrock-core-adminmenu');
        $this->mergeConfigFrom( __DIR__.'/config/larrock-sitemap.php', 'larrock-sitemap');
        $this->mergeConfigFrom( __DIR__.'/config/larrock-to-dashboard.php', 'larrock-to-dashboard');
        $this->mergeConfigFrom( __DIR__.'/config/larrock.php', 'larrock');

        $this->app->bind('command.larrock:install', LarrockInstallCommand::class);
        $this->app->bind('command.larrock:check', LarrockCheckCommand::class);
        $this->app->bind('command.larrock:updateEnv', LarrockUpdateEnvCommand::class);
        $this->app->bind('command.larrock:updateVendorConfig', LarrockUpdateVendorConfigCommand::class);
        $this->app->bind('command.larrock:manager', LarrockManagerCommand::class);
        $this->app->bind('command.larrock:renamePublicDirectory', LarrockRenamePublicDirectoryCommand::class);
        $this->app->bind('command.larrock:assets', LarrockAssetsCommand::class);
        $this->commands([
            'command.larrock:install',
            'command.larrock:check',
            'command.larrock:updateEnv',
            'command.larrock:updateVendorConfig',
            'command.larrock:manager',
            'command.larrock:addAdmin',
            'command.larrock:renamePublicDirectory',
            'command.larrock:assets',
        ]);

        $this->app->bind(DefaultFilesystem::class, MediaFilesystem::class);
    }
}